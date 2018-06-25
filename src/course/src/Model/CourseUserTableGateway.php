<?php

namespace Course\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator;

class CourseUserTableGateway extends TableGateway
{
    /**
     * CourseUserTableGateway constructor.
     *
     * @param AdapterInterface $adapter
     * @param null $features
     * @param null $resultSetPrototype
     * @param null $sql
     */
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new Course);
        parent::__construct('course_user', $adapter, $features, $resultSetPrototype, $sql);
    }
}
