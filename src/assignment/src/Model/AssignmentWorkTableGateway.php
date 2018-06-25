<?php

namespace Assignment\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class AssignmentWorkTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        parent::__construct('assignment_work', $adapter, $features, $resultSetPrototype, $sql);
    }
}
