<?php

namespace Admin\User\Factory\Action\Form;

use Psr\Container\ContainerInterface;
use Admin\User\Form;
use User\Action;
use User\Model;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Admin
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);
        $userTable = $container->get(Model\UserTable::class);

        $urlHelper = $container->get(UrlHelper::class);
        $form = new Form\Admin($urlHelper('admin/post/admin'));

        return new Action\Form\User($template, $userTable, $form);
    }
}
