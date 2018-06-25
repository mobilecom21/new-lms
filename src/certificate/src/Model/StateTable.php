<?php

namespace Certificate\Model;

use Certificate;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class StateTable
{
    /**
     * @var PaymentTableGateway
     */
    private $tableGateway;

    public function __construct(StateTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function fetchByCountryId(int $id)
    {
        if (!$id) {
            return null;
        }

        return $this->tableGateway->select(['country_id' => $id]);
    }

	public function fetchAll()
    {
        return $this->tableGateway->select();
    }

	public function fetchAllToArray()
    {
        return $this->tableGateway->select()->toArray();
    }

	public function fetchAllOrderByIdDesc()
    {
        return $this->tableGateway->select(function (Select $select) {$select->order('id DESC');});
    }
}