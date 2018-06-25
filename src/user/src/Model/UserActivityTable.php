<?php

namespace User\Model;

use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class UserActivityTable
{
    /**
     * @var UserActivityTableGateway
     */
    private $userActivityTableGateway;

    public function __construct(UserActivityTableGateway $userActivityTableGateway)
    {
        $this->userActivityTableGateway = $userActivityTableGateway;
    }

    public function fetchByUserId(int $userId): ?AbstractResultSet
    {
        $select = $this->userActivityTableGateway->getSql()->select()
            ->where([
                'user_id = ?' => $userId
            ])
            ->order('id DESC');
        return $this->userActivityTableGateway->selectWith($select);
    }

    public function add(int $userId, string $text): void
    {
        $this->userActivityTableGateway->insert([
            'user_id' => $userId,
            'text' => $text,
            'created_at' => time()
        ]);
    }

    public function getTableName()
    {
        return $this->userActivityTableGateway->getTable();
    }
}
