<?php

namespace App\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use User\Model\UserMetaTable;
use User\Model\UserActivityTable;
use User\Model\UserLoginTable;
use Options\Model\OptionsTable;
use Course\Model\CourseUserTable;
use Course\Model\ContentTable;
use	Exam\Model\ExamTable;

class HomePageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $router   = $container->get(RouterInterface::class);
        $template = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;

        return new HomePageAction($router, $template, $container->get(UserMetaTable::class), $container->get(UserActivityTable::class), $container->get(UserLoginTable::class), $container->get(OptionsTable::class), $container->get(CourseUserTable::class), $container->get(ContentTable::class), $container->get(ExamTable::class));
    }
}
