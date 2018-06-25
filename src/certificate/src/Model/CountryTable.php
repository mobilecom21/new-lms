<?php

namespace Certificate\Model;

use Certificate;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class CountryTable
{
    /**
     * @var PaymentTableGateway
     */
    private $tableGateway;

    public function __construct(CountryTableGateway $tableGateway)
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