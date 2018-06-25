<?php

namespace User\Model;

use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class UserOnlineTable
{
    /**
     * @var UserOnlineTableGateway
     */
    private $userOnlineTableGateway;

    public function __construct(UserOnlineTableGateway $userOnlineTableGateway)
    {
        $this->userOnlineTableGateway = $userOnlineTableGateway;
    }

    public function fetchByUserId(int $userId): ?AbstractResultSet
    {
        return $this->userOnlineTableGateway->select(['user_id' => $userId]);
    }

    public function add(int $userId): void
    {
        $oldEntry = $this->userOnlineTableGateway->select(['user_id' => $userId]);
        if ($oldEntry->count()) {
            $this->userOnlineTableGateway->update([
                'created_at' => time()
            ], [
                'user_id' => $userId
            ]);
        } else {
            $this->userOnlineTableGateway->insert([
                'user_id' => $userId,
                'created_at' => time()
            ]);
        }
    }

    public function clean(): void
    {
        $time = time();
        $time_check = $time - 300;
        $where = new Where();
        $where->lessThan('created_at', $time_check);
        $this->userOnlineTableGateway->delete($where);
    }

    public function getTableName()
    {
        return $this->userOnlineTableGateway->getTable();
    }
}
