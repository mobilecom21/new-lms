<?php

namespace User\View\Helper;

use User\Model;

class User
{
    /**
     * @var Model\UserTable
     */
    private $userTable;

    public function __construct(Model\UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    public function __invoke(int $userId): Model\User
    {
        return $this->userTable->oneById($userId);
    }
}
