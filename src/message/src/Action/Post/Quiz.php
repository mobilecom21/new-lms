<?php

namespace Message\Action\Post;

use Message\InputFilter;
use Message\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tutor;
use User;
use Options;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;
use Zend\Expressive\Helper\UrlHelper;


class Quiz
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
     * @var Tutor\Model\TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var Options
     */
    private $optionsTable;

    /**
     * @var Mail\Transport\TransportInterface
     */
    private $transportMail;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\MessageTable $messageTable,
        Tutor\Model\TutorStudentCourseTable $tutorStudentCourseTable,
        User\Model\UserTable $userTable,
        User\Model\UserMetaTable $userMetaTable,
        Options\Model\OptionsTable $optionsTable,
        Mail\Transport\TransportInterface $transportMail,
        UrlHelper $urlHelper
    ) {
        $this->template = $template;
        $this->messageTable = $messageTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->optionsTable = $optionsTable;
        $this->transportMail = $transportMail;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $courseId = $request->getAttribute('courseId');
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Quiz();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $yesNoFilter = function (string $input) {
            if ('1' === $input) {
                return 'Yes';
            }

            return '2' === $input ? 'No' : $input;
        };

        $labelIt = function (string $key) {
            switch ($key) {
                case 'is_course':
                    return 'Is your enquiry related to your course:';
                case 'is_content':
                    return 'Is your enquiry related to one of your modules:';
                case 'content':
                    return 'Which module:';
                case 'attachment':
                    return 'Which of the following does this relate to:';
                case 'text':
                    return 'Message';
            }
            return $key;
        };

        $textArray = [];
        foreach ($data as $key => $value) {
            if ('submit' === $key || empty($value)) continue;
            $textArray[$labelIt($key)] = $yesNoFilter($value);
            if ('is_course' === $key) {
                $textArray[$labelIt($key)] = '<a href="' . ($this->urlHelper)('student/course/view/single', ['id' => $courseId]) . '">' . $textArray[$labelIt($key)] . '</a>';
            }
        }

        $data['text'] = json_encode($textArray);

        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);

        /**
         * @var Tutor\Model\TutorStudentCourse $tutorStudentCourse
         */
        $tutorStudentCourse = $this->tutorStudentCourseTable->fetchTutorForStudentAndCourse($currentUser->getId(), $courseId);

        if (!$tutorStudentCourse) {
            return new JsonResponse([]);
        }

        $data['sender'] = (int) $currentUser->getId();
        $data['receiver'] = (int) $tutorStudentCourse->getTutorId();

        // send to support user
        if (2 === (int) $filter->getValue('is_course')) {
            $admins = $this->userTable->byRole('Rbac\Role\Administrator')->toArray();
            $data['receiver'] = $admins[0]['id'];
        }

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

        $message = new Mail\Message();

        if ($this->optionsTable->optionExists('from_email')) {
            $from = $this->optionsTable->fetchByName('from_email')['value'];
            $message->addTo($receiver->getIdentity())
                ->addFrom($from)
                ->setSubject('You have received a message about your course')
                ->setBody($body);
            $this->transportMail->send($message);
        }

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('student/course/view/single', ['id' => $courseId])
        ]);
    }
}
