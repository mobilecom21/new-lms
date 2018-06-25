<?php

namespace Message\Action\Form;

use Message\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User;
use Zend\Diactoros\Response\JsonResponse;

class Unread
{
    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\MessageTable
     */
    private $messageTable;


    public function __construct(
        Model\MessageTable $messageTable,
        User\Model\UserTable $userTable
    )
    {
        $this->messageTable = $messageTable;
        $this->userTable = $userTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();

        $unread = $this->messageTable->countUnread($currentUserId);

        return new JsonResponse([
            'unread' => $unread
        ]);
    }
}