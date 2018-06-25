<?php

namespace Message\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator;


class MessageTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new ResultSet\HydratingResultSet(new Hydrator\ClassMethods, new Message);
        parent::__construct('message', $adapter, $features, $resultSetPrototype, $sql);
    }
}
