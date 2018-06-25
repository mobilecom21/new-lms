<?php

namespace User\Action\Post;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Model as UserModel;
use Course\Model as CourseModel;
use Options;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;


class Resendemail
{

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var UserModel\UserTable
     */
    private $userTable;

    /**
     * @var UserModel\courseUserTable
     */
    private $courseUserTable;

    /**
     * @var Options
     */
    private $optionsTable;

    public function __construct(
        UrlHelper $urlHelper, 
        UserModel\UserTable $userTable, 
        CourseModel\CourseUserTable $courseUserTable,
        Options\Model\OptionsTable $optionsTable,
        Template\TemplateRendererInterface $template,
        Mail\Transport\TransportInterface $transportMail
        )
    {
        $this->urlHelper = $urlHelper;
        $this->userTable = $userTable;
        $this->courseUserTable = $courseUserTable;
        $this->optionsTable = $optionsTable;
        $this->template = $template;
        $this->transportMail = $transportMail;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        //fetch POST data (should be just 'id')
        $data = $request->getParsedBody() ?? [];

        //fetch the user info to reference later
        $user = $this->userTable->oneById($data['id']);

        //fetch a course name to reference in the email body
        $courses = $this->courseUserTable->fetchByUserId($data['id'])->toArray();
        $coursename = $courses[0]['name'];
        
        //generate a new password because we can't retrieve their original one
        // (copies JS code in "generate password" or "generate PIN" for add user)
        $length = 8;
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = strlen($charset);
        $password = '';
        for($i=0; $i<$length; $i++) {
            $password .= $charset[ rand(0, $count) ];
        }

        //reset the user password
        $this->userTable->updatePassword($data['id'], $password);
        $this->userTable->updateToken($data['id'], '');

        //resend welcome email to student
        $htmlMarkup = $this->template->render('emails::newaccountstudent', [
            'layout' => false,
            'fullName' => $user->getFirstName() . ' ' . $user->getLastName(),
            'username' => $user->getUserName(),
            'password' => $password,
            'pin' => $user->getPlainPin(),
            'courseName' => $coursename
        ]);
        
        $html = new MimePart($htmlMarkup);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $message = new Mail\Message();

        if ($this->optionsTable->optionExists('from_email')) {
            $from = $this->optionsTable->fetchByName('from_email')['value'];
            $message->addTo($user->getIdentity())
                ->addFrom($from)
                ->setSubject('Welcome to NCC Home Learning')
                ->setBody($body);
            $status = $this->transportMail->send($message);
        }

        return new JsonResponse([
            'successMessage' => 'Email resent'
        ]);
    }
}
