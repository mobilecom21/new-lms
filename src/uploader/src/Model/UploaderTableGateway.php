<?php

namespace Uploader\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator;
use Zend\Db\TableGateway\TableGateway;

class UploaderTableGateway extends TableGateway
{
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new Uploader);
        parent::__construct('uploader', $adapter, $features, $resultSetPrototype, $sql);
    }
}
