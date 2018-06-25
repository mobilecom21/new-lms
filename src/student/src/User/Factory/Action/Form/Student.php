<?php

namespace Student\User\Factory\Action\Form;

use Psr\Container\ContainerInterface;
use Student\User\Form;
use User\Action;
use User\Model;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Student
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);
        $userTable = $container->get(Model\UserTable::class);

        /**
         * @var UrlHelper $urlHelper
         */
        $urlHelper = $container->get(UrlHelper::class);
        $fieldsetCourseTutor = $container->get(Form\Fieldset\CourseTutor::class);
        $form = new Form\Student($fieldsetCourseTutor, $urlHelper('student/post/student'));

        return new Action\Form\User($template, $userTable, $form);
    }
}
