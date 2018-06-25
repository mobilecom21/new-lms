<?php

namespace Uploader\Model;

use Zend\Db\Sql\Select;

class UploaderTable
{
    /**
     * @var UploaderTableGateway
     */
    private $tableGateway;

    public function __construct(UploaderTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchById(?int ...$ids)
    {
        return $this->tableGateway->select([
            'id' => $ids
        ]);
    }

    public function fetchByIdPathSize(int $id, string $path, int $size)
    {
        return $this->tableGateway->select([
            'id' => $id,
            'path' => $path,
            'size' => $size
        ])->current();
    }

    public function fetchByIds($ids)
    {
        return $this->tableGateway->select(['id' => $ids]);
    }

    public function insert(string $path, string $name, string $type, string $size, $uploadedBy): int
    {
        $this->tableGateway->insert([
            'path' => $path,
            'name' => $name,
            'type' => $type,
            'size' => $size,
            'uploaded_by' => $uploadedBy
        ]);

        return $this->tableGateway->lastInsertValue;
    }

    public function delete(int ...$ids)
    {
        return $this->tableGateway->delete([
            'id' => $ids
        ]);
    }

    public function joinWith(Select $select, string $on, $columns = Select::SQL_STAR, $type = Select::JOIN_LEFT)
    {
        return $select->join($this->tableGateway->getTable(), $on . ' = uploader.id', $columns, $type);
    }
}
