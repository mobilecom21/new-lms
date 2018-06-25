<?php

namespace EventLogger\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator;
use Zend\Db\TableGateway\TableGateway;

class EventLoggerTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new EventLogger);
        parent::__construct('options', $adapter, $features, $resultSetPrototype, $sql);
    }
}
