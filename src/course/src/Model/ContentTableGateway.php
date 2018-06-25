<?php

namespace Course\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class ContentTableGateway extends TableGateway
{
    /**
     * ContentTableGateway constructor.
     *
     * @param AdapterInterface $adapter
     * @param null $features
     * @param null $resultSetPrototype
     * @param null $sql
     */
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? (new ResultSet())->setArrayObjectPrototype(new Content());
        parent::__construct('course_content', $adapter, $features, $resultSetPrototype, $sql);
    }
}
