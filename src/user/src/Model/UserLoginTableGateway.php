<?php

namespace User\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator;
use Zend\Db\TableGateway\TableGateway;

class UserLoginTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new UserLogin);
        parent::__construct('user_login', $adapter, $features, $resultSetPrototype, $sql);
    }
}
