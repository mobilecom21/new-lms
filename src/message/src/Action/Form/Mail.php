<?php

namespace Message\Action\Form;

use Message;
use User;
use Tutor\Model\TutorStudentCourseTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Mail
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Message\Form\Mail
     */
    private $mailForm;

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


    public function __construct(
        Message\Form\Mail $mailForm,
        User\Model\UserTable $userTable,
        User\Model\UserMetaTable $userMetaTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        Template\TemplateRendererInterface $template
    )
    {
        $this->template = $template;
        $this->mailForm = $mailForm;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $receiverId = $request->getAttribute('userId');

        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);

        /**
         * @var User\Model\User $receiver
         */
        $receiver = $this->userTable->oneById($receiverId);

        if ('Rbac\Role\Tutor' == $currentUser->getRole() && 'Rbac\Role\Tutor' == $receiver->getRole()) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        if ('Rbac\Role\Tutor' == $currentUser->getRole()
            && 'Rbac\Role\Student' == $receiver->getRole()
            && ! $this->tutorStudentCourseTable->isTutorForStudent($currentUser->getId(), $receiverId)
        ) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        /**
         * @var User\Model\UserMetaTable $receiver
         */
        $receiverMetas = $this->userMetaTable->fetchByUserId($receiverId)->toArray();
        $first_name = $last_name = '';
        foreach($receiverMetas as $receiverMeta) {
            if ('first_name' == $receiverMeta['name']) {
                $first_name = $receiverMeta['value'];
            } elseif ('last_name' == $receiverMeta['name']) {
                $last_name = $receiverMeta['value'];
            }
        }

        return new HtmlResponse($this->template->render('message::form/mail', [
            'form' => $this->mailForm,
            'userId' => $receiverId,
            'userName' => $first_name . ' ' . $last_name
        ]));
    }
}