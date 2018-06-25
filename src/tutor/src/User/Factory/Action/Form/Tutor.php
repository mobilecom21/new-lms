<?php

namespace Tutor\User\Factory\Action\Form;

use Course;
use Psr\Container\ContainerInterface;
use Tutor\User\Form;
use User\Action;
use User\Model;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Tutor
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);
        $userTable = $container->get(Model\UserTable::class);

        $urlHelper = $container->get(UrlHelper::class);
        $courseSelectElement = $container->get(Course\Form\Element\Select\Course::class);
        $form = new Form\Tutor($courseSelectElement, $urlHelper('tutor/post/tutor'));

        return new Action\Form\User($template, $userTable, $form);
    }
}
