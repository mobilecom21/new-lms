<?php

namespace Admin\User\Factory\Action\Post;

use Psr\Container\ContainerInterface;
use Admin\User\InputFilter;
use User\Action;
use User\Model\UserTable;
use User\Model\UserMetaTable;
use Course\Model\CourseTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use Assignment\Model\AssignmentWorkTable;
use Tutor\Model\TutorStudentCourseTable;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Mail;
use Zend\Expressive\Template;

class Admin
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);
        $userTable = $container->get(UserTable::class);
        $userMetaTable = $container->get(UserMetaTable::class);
        $courseTable = $container->get(CourseTable::class);
        $attachmentTable = $container->get(AttachmentTable::class);
        $contentTable = $container->get(ContentTable::class);
        $assignmentWorkTable = $container->get(AssignmentWorkTable::class);
        $tutorStudentCourseTable = $container->get(TutorStudentCourseTable::class);
        $urlHelper = $container->get(UrlHelper::class);
        $transportMail = $container->get(Mail\Transport\TransportInterface::class);
        $inputFilter = new InputFilter\Admin();

        return new Action\Post\User(
            $template,
            $userTable,
            $userMetaTable,
            $courseTable,
            $attachmentTable,
            $contentTable,
            $assignmentWorkTable,
            $tutorStudentCourseTable,
            $inputFilter,
            $urlHelper('admin/user/view/resultset'),
            $transportMail
        );
    }
}