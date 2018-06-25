<?php

namespace Student\Course\Factory\Action\View\ResultSet;

use Course;
use Psr\Container\ContainerInterface;
use User;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template;

class ByUserId
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);

        /**
         * @var Course\Model\CourseUserTable $courseUserTable
         */
        $courseUserTable = $container->get(Course\Model\CourseUserTable::class);

        /**
         * @var AuthenticationService $authenticationService
         */
        $authenticationService = $container->get(AuthenticationService::class);

        /**
         * @var User\Model\User $user
         */
        $user = $authenticationService->getStorage()->read();

        return new Course\Action\View\ResultSet\ByUserId(
            $template,
            $courseUserTable,
            $user->getId(),
            'student/course/view/single'
        );
    }
}
