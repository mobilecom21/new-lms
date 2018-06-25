<?php

namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use User;
use Options;
use Zend\Session\Container;
use Course;
use Exam;

class HomePageAction implements ServerMiddlewareInterface
{
    private $router;

    private $template;

    private $usermetaTable;

    private $userActivityTable;

    private $userLoginTable;

    private $optionsTable;

    private $courseUserTable;

    private $contentTable;

    private $examTable;

    public function __construct(
        Router\RouterInterface $router,
        Template\TemplateRendererInterface $template = null,
        User\Model\UserMetaTable $usermetaTable,
        User\Model\UserActivityTable $userActivityTable,
        User\Model\UserLoginTable $userLoginTable,
        Options\Model\OptionsTable $optionsTable,
        Course\Model\CourseUserTable $courseUserTable,
        Course\Model\ContentTable $contentTable,
        Exam\Model\ExamTable $examTable
    )
    {
        $this->router   = $router;
        $this->template = $template;
        $this->usermetaTable = $usermetaTable;
        $this->userActivityTable = $userActivityTable;
        $this->userLoginTable = $userLoginTable;
        $this->optionsTable = $optionsTable;
        $this->courseUserTable = $courseUserTable;
        $this->contentTable = $contentTable;
        $this->examTable = $examTable;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (! $this->template) {
            return new JsonResponse([
                'welcome' => 'Welcome to LMS Home Page'
            ]);
        }

        $data = [];

        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();

        $data['activity'] = $this->userActivityTable->fetchByUserId($currentUserId);
        $data['login'] = $this->userLoginTable->fetchByUserId($currentUserId);
        $data['options'] = $this->optionsTable->fetchAll()->toArray();

        return new HtmlResponse($this->template->render('app::home-page', $data));
    }
}
