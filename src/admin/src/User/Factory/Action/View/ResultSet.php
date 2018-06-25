<?php

namespace Admin\User\Factory\Action\View;

use Psr\Container\ContainerInterface;
use User;
use Course;
use Tutor;
use Zend\Expressive\Template;

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
         * @var Tutor\Model\TutorStudentCourseTable $tutorStudentCourseTable
         */
        $tutorStudentCourseTable = $container->get(Tutor\Model\TutorStudentCourseTable::class);

        return new User\Action\View\ResultSet(
            $template,
            $userTable,
            $courseTable,
            $tutorStudentCourseTable,
            'admin::resultset',
            'Rbac\Role\Administrator'
        );
    }
}
