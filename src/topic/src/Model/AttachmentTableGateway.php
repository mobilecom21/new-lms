<?php

namespace Topic\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator;
use Zend\Db\TableGateway\TableGateway;

class AttachmentTableGateway extends TableGateway
{
    /**
     * AttachmentTableGateway constructor.
     *
     * @param AdapterInterface $adapter
     * @param null $features
     * @param null $resultSetPrototype
     * @param null $sql
     */
    public function __construct(AdapterInterface $adapter, $features = null, $resultSetPrototype = null, $sql = null)
    {
        $resultSetPrototype = $resultSetPrototype ?? new HydratingResultSet(new Hydrator\ClassMethods, new Attachment);
        parent::__construct('topic_attachment', $adapter, $features, $resultSetPrototype, $sql);
    }
}
