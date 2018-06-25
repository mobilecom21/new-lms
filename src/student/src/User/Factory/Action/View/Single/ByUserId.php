<?php

namespace Student\User\Factory\Action\View\Single;

use Psr\Container\ContainerInterface;
use User;
use Course\Model\ContentTable;
use Course\Model\CourseTable;
use Topic\Model\AttachmentTable;
use Tutor\Model\TutorStudentCourseTable;
use Assignment\Model\AssignmentWorkTable;
use Zend\Expressive\Template;
use Zend\Authentication\AuthenticationService;

class ByUserId
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);

        /**
         * @var User\Model\userTable $userTable
         */
        $userTable = $container->get(User\Model\UserTable::class);

        /**
         * @var User\Model\userMetaTable $userMetaTable
         */
        $userMetaTable = $container->get(User\Model\UserMetaTable::class);

        /**
         * @var TutorStudentCourseTable
         */
        $tutorStudentCourseTable = $container->get(TutorStudentCourseTable::class);

        /**
         * @var AssignmentWorkTable
         */
        $assignmentWorkTable = $container->get(AssignmentWorkTable::class);

        /**
         * @var ContentTable
         */
        $contentTable = $container->get(ContentTable::class);

        /**
         * @var CourseTable
         */
        $courseTable = $container->get(CourseTable::class);

        /**
         * @var $attachmentTable
         */
        $attachmentTable = $container->get(AttachmentTable::class);

        /**
         * @var AuthenticationService $authenticationService
         */
        $authenticationService = $container->get(AuthenticationService::class);

        /**
         * @var User\Model\User $user
         */
        $user = $authenticationService->getStorage()->read();

        return new User\Action\View\Single\ByUserId(
            $template,
            $userTable,
            $userMetaTable,
            $tutorStudentCourseTable,
            $assignmentWorkTable,
            $contentTable,
            $courseTable,
            $attachmentTable,
            $user->getId(),
            $user->getRole(),
            'student/user/view/single',
            'student::single'
        );
    }
}
