<?php

namespace Tutor\User\Factory\Action\View;

use Psr\Container\ContainerInterface;
use User;
use Zend\Expressive\Template;
use Course;
use Tutor;

class ResultSet
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);

        /**
         * @var User\Model\UserTable $userTable
         */
        $userTable = $container->get(User\Model\UserTable::class);

        /**
         * @var Course\Model\CourseTable $courseTable
         */
        $courseTable = $container->get(Course\Model\CourseTable::class);

        /**
         * @var Course\Model\CourseTable $courseTable
         */
        $tutorStudentCourseTable = $container->get(Tutor\Model\TutorStudentCourseTable::class);

        return new User\Action\View\ResultSet(
            $template,
            $userTable,
            $courseTable,
            $tutorStudentCourseTable,
            'tutor::resultset',
            'Rbac\Role\Tutor'
        );
    }
}
