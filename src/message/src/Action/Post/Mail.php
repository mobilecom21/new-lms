<?php

namespace Message\Action\Post;

use Message\InputFilter;
use Message\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User;
use Tutor\Model\TutorStudentCourseTable;
use Options;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Mail as ZendMail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;
use Zend\Expressive\Helper\UrlHelper;


class Mail
{

    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\MessageTable
     */
    private $messageTable;

    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var Options
     */
    private $optionsTable;

    /**
     * @var ZendMail\Transport\TransportInterface
     */
    private $transportMail;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\MessageTable $messageTable,
        User\Model\UserTable $userTable,
        User\Model\UserMetaTable $userMetaTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        Options\Model\OptionsTable $optionsTable,
        ZendMail\Transport\TransportInterface $transportMail,
        UrlHelper $urlHelper
    ) {
        $this->template = $template;
        $this->messageTable = $messageTable;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->optionsTable = $optionsTable;
        $this->transportMail = $transportMail;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $userId = $request->getAttribute('userId');
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Mail();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = $currentUser->getId();
        $currentUserRole = $currentUser->getRole();
        $isAllowed = false;
        $admins = $this->userTable->byRole('Rbac\Role\Administrator')->toArray();
        if ('Rbac\Role\Administrator' == $currentUserRole) {
            $isAllowed = true;
        } elseif ('Rbac\Role\Tutor' == $currentUserRole && $this->tutorStudentCourseTable->isTutorForStudent($currentUserId, $userId)) {
            $isAllowed = true;
        } elseif ('Rbac\Role\Student' == $currentUserRole && $this->tutorStudentCourseTable->isTutorForStudent($userId, $currentUserId)) {
            $isAllowed = true;
        } elseif ('Rbac\Role\Student' == $currentUserRole && $admins[0]['id'] == $userId) {
            $isAllowed = true;
        }

        if(! $isAllowed) {
            return new JsonResponse([
                'redirectTo' => ($this->urlHelper)('message/view/resultset')
            ]);
        }

        $data['text'] = json_encode(['Message' => $data['text']]);
        $data['sender'] = (int) $currentUser->getId();
        $data['receiver'] = (int) $userId;

        /**
         * @var User\Model\User $receiver
         */
        $receiver = $this->userTable->oneById($data['receiver']);

        $this->messageTable->insert($data);

        $first_name = $this->userMetaTable->getMetaByName($data['receiver'], 'first_name')->current();
        $last_name = $this->userMetaTable->getMetaByName($data['receiver'], 'last_name')->current();

        $htmlMarkup = $this->template->render('emails::message', [
            'layout' => false,
            'fullName' => $first_name->getValue() . ' ' . $last_name->getValue(),
        ]);
        $html = new MimePart($htmlMarkup);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $message = new ZendMail\Message();

        if ($this->optionsTable->optionExists('from_email')) {
            $from = $this->optionsTable->fetchByName('from_email')['value'];
            $message->addTo($receiver->getIdentity())
                ->addFrom($from)
                ->setSubject('You have received a message about your course')
                ->setBody($body);
            $this->transportMail->send($message);
        }

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('message/view/resultset')
        ]);
    }
}
