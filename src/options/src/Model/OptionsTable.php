<?php

namespace Options\Model;

use Zend\Db\Sql\Where;

class OptionsTable
{
    /**
     * @var OptionsTableGateway
     */
    private $tableGateway;

    public function __construct(OptionsTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchByName($name)
    {
        $where = new Where();
        $where->EqualTo('name', $name);
        $statement = $this->tableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->tableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        return $statement->execute()->current();
    }

    public function save(array $data)
    {
        foreach ($data as $name => $value)
        {
            if ($this->optionExists($name)) {
                $this->tableGateway->update([
                    'value' => $value
                ], ['name' => $name]);
            } else {
                $this->tableGateway->insert([
                    'name' => $name,
                    'value' => $value
                ]);
            }
        }
    }

    public function optionExists($name)
    {
        $where = new Where();
        $where->EqualTo('name', $name);
        $statement = $this->tableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->tableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $optionCollection = $statement->execute();
        if ($optionCollection->count() > 0) {
            return true;
        }
        return false;
    }
}
