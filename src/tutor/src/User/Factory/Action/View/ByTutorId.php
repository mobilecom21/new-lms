<?php

namespace Tutor\User\Factory\Action\View;

use Psr\Container\ContainerInterface;
use User;
use Tutor\Model\TutorStudentCourseTable;
use Assignment\Model\AssignmentWorkTable;
use Zend\Expressive\Template;
use Zend\Authentication\AuthenticationService;

class ByTutorId
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
         * @var AuthenticationService $authenticationService
         */
        $authenticationService = $container->get(AuthenticationService::class);

        /**
         * @var User\Model\User $user
         */
        $user = $authenticationService->getStorage()->read();

        return new User\Action\View\Single\ByTutorId(
            $template,
            $userTable,
            $userMetaTable,
            $tutorStudentCourseTable,
            $assignmentWorkTable,
            $user->getId(),
            $user->getRole(),
            'tutor/view/single',
            'tutor::single'
        );
    }
}
