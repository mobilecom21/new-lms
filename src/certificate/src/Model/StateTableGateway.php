<?php

namespace Certificate\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator;

use Zend\Db\ResultSet\ResultSet;

class StateTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? (new ResultSet())->setArrayObjectPrototype(new State());
        parent::__construct('states', $adapter, $features, $resultSetPrototype, $sql);
    }
}