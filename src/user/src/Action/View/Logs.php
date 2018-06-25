<?php

namespace User\Action\View;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Logs
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var User\Model\UserLoginTable
     */
    private $userLoginTable;

    public function __construct(
        Template\TemplateRendererInterface $template,
        User\Model\UserTable $userTable,
        User\Model\UserLoginTable $userLoginTable,
        User\Model\UserMetaTable $userMetaTable)
    {
        $this->template = $template;
        $this->userTable = $userTable;
        $this->userLoginTable = $userLoginTable;
        $this->userMetaTable = $userMetaTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $user_id = $request->getAttribute('id');
        $logs = $this->userLoginTable->fetchByUserId($user_id)->toArray();

        return new HtmlResponse($this->template->render('student::logs', [
            'resultSet' => $logs,
            'usermeta' => $this->userMetaTable->fetchByUserId($user_id)->toArray(),
        ]));
    }
}
