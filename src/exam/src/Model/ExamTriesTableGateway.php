<?php

namespace Exam\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator;

use Zend\Db\ResultSet\ResultSet;

/*class ExamTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new Exam);
        parent::__construct('exam', $adapter, $features, $resultSetPrototype, $sql);
    }
}*/

class ExamTriesTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? (new ResultSet())->setArrayObjectPrototype(new ExamTries());
        parent::__construct('exam_tries', $adapter, $features, $resultSetPrototype, $sql);
    }
}