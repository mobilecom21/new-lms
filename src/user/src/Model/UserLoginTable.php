<?php

namespace User\Model;

use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class UserLoginTable
{
    /**
     * @var UserLoginTableGateway
     */
    private $userLoginTableGateway;

    public function __construct(UserLoginTableGateway $userLoginTableGateway)
    {
        $this->userLoginTableGateway = $userLoginTableGateway;
    }

    public function fetchByUserId(int $userId): ?AbstractResultSet
    {
        $select = $this->userLoginTableGateway->getSql()->select()
            ->where([
                'user_id = ?' => $userId
            ])
            ->order('id DESC');
        return $this->userLoginTableGateway->selectWith($select);
    }

    public function add(int $userId, string $ip): void
    {
        $this->userLoginTableGateway->insert([
            'user_id' => $userId,
            'ip' => $ip,
            'created_at' => time()
        ]);
    }

    public function getTableName()
    {
        return $this->userLoginTableGateway->getTable();
    }
}
