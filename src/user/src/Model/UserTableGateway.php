<?php

namespace User\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class UserTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? (new ResultSet())->setArrayObjectPrototype(new User());
        parent::__construct('user', $adapter, $features, $resultSetPrototype, $sql);
    }
}
