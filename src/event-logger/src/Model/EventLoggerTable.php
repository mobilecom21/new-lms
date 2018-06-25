<?php

namespace EventLogger\Model;

use EventLogger\Model\EventLoggerTableGateway;

class EventLoggerTable
{
    /**
     * @var OptionsTableGateway
     */
    private static $tableGateway;

    public function __construct(EventLoggerTableGateway $tableGateway)
    {

        $this->tableGateway = $tableGateway;
    }

    
    public function log()
    {

//id, user_id, affected_user, event_type, description


         $this->tableGateway->insert([
            'user_id' => 1,
            'affected_user' => 1,
            'event_type' => "test",
            'description' => "test"
        ]);
    }


/*
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

    */
}
