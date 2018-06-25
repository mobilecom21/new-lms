<?php

namespace Assignment\Action\Form;

use Assignment\Model\AssignmentWorkTable;
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
     * @var AssignmentWorkTable
     */
    private $assignmentWorkTable;


    public function __construct(
        AssignmentWorkTable $assignmentWorkTable,
        User\Model\UserTable $userTable
    )
    {
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->userTable = $userTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();

        $unread = $this->assignmentWorkTable->countUnread($currentUserId);

        return new JsonResponse([
            'unread' => $unread
        ]);
    }
}