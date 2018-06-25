<?php

namespace Attempt\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator;

use Zend\Db\ResultSet\ResultSet;

/*class AttemptTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new Attempt);
        parent::__construct('exam_attempt', $adapter, $features, $resultSetPrototype, $sql);
    }
}*/

class AttemptTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? (new ResultSet())->setArrayObjectPrototype(new Attempt());
        parent::__construct('exam_attempt', $adapter, $features, $resultSetPrototype, $sql);
    }
}