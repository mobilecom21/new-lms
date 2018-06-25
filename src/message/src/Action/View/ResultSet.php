<?php

namespace Message\Action\View;

use Message\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class ResultSet
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
     * @var Model\MessageTable
     */
    private $messageTable;

    /**
     * @var User\Model\UserOnlineTable
     */
    private $userOnlineTable;

    public function __construct(
        Template\TemplateRendererInterface $template,
        User\Model\UserTable $userTable,
        User\Model\UserOnlineTable $userOnlineTable,
        Model\MessageTable $messageTable)
    {
        $this->template = $template;
        $this->userTable = $userTable;
        $this->userOnlineTable = $userOnlineTable;
        $this->messageTable = $messageTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = $currentUser->getId();
        $currentUserRole = $currentUser->getRole();
        if ('Rbac\Role\Administrator' == $currentUserRole) {
            $admins = $this->userTable->byRole('Rbac\Role\Administrator')->toArray();
            $currentUserId = $admins[0]['id'];
        }

        $this->userOnlineTable->add($currentUserId);
        $this->userOnlineTable->clean();

        $search = $request->getQueryParams()['search'] ?? '';

        $received = $this->messageTable->fetchReceived($currentUserId, $search)->toArray();
        $sent = $this->messageTable->fetchSent($currentUserId, $search)->toArray();
        $results = $contacts = array();
        foreach ($received ?? [] as $v) {
            $results[$v['sender']] = [];
            $contacts[$v['sender']] = [];
        }
        foreach ($sent ?? [] as $v) {
            $results[$v['receiver']] = [];
            $contacts[$v['sender']] = [];
        }
        foreach ($received as $v) {
            $contacts[$v['sender']][$v['id']] = $v;
            if (! empty($v['hide_to_receiver']) && $currentUserId = $v['receiver']) {
                continue;
            }
            if (isset($results[$v['sender']])) {
                $results[$v['sender']][$v['id']] = $v;
            }
        }
        foreach ($sent as $v) {
            $contacts[$v['receiver']][$v['id']] = $v;
            if (! empty($v['hide_to_sender']) && $currentUserId = $v['sender']) {
                continue;
            }
            if (isset($results[$v['receiver']])) {
                $results[$v['receiver']][$v['id']] = $v;
            }
        }

        return new HtmlResponse($this->template->render('message::resultset', [
            'results' => $results,
            'contacts' => $contacts,
            'search' => $search
        ]));
    }
}
