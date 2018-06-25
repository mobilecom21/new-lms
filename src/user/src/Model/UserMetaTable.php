<?php

namespace User\Model;

use Zend\Db\ResultSet\AbstractResultSet;

class UserMetaTable
{
    /**
     * @var UserMetaTableGateway
     */
    private $userMetaTableGateway;

    public function __construct(UserMetaTableGateway $userMetaTableGateway)
    {
        $this->userMetaTableGateway = $userMetaTableGateway;
    }

    public function fetchByUserId(int $userId): ?AbstractResultSet
    {
        return $this->userMetaTableGateway->select(['user_id' => $userId]);
    }

    public function add(int $userId, string $name, string $value): void
    {
        $this->userMetaTableGateway->insert([
            'user_id' => $userId,
            'name' => $name,
            'value' => $value
        ]);
    }

    public function update(int $userId, string $name, string $value): int
    {
        return $this->userMetaTableGateway->update([
            'value' => $value
        ], [
            'user_id' => $userId,
            'name' => $name
        ]);
    }

    public function getMetaByName(int $userId, string $name): ?AbstractResultSet
    {
        return $this->userMetaTableGateway->select(['user_id' => $userId, 'name' => $name]);
    }

    public function getTableName()
    {
        return $this->userMetaTableGateway->getTable();
    }
}
