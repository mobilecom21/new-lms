<?php

namespace Topic\Model;

use Course\Model\ContentTable as CourseContentTable;
use Zend\Db\ResultSet\ResultSet;

class TopicTable
{
    /**
     * @var TopicTableGateway
     */
    private $tableGateway;

    /**
     * @var CourseContentTable
     */
    private $courseContentTable;

    public function __construct(TopicTableGateway $tableGateway, CourseContentTable $courseContentTable)
    {
        $this->tableGateway = $tableGateway;
        $this->courseContentTable = $courseContentTable;
    }

    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function save(array $data): int
    {
        if (!empty($data['id'])) {
            return $this->tableGateway->update([
                'name' => $data['name'],
                'summary' => $data['summary'],
                'required' => $data['required']
            ], ['id' => $data['id']]);
        }

        $this->tableGateway->insert([
            'name' => $data['name'],
            'summary' => $data['summary'],
            'required' => $data['required']
        ]);

        $lastInsertValue = $this->tableGateway->lastInsertValue;

        // link topic with course
        $this->courseContentTable->add($data['parentId'], 'Topic', $lastInsertValue);

        return $lastInsertValue;
    }
}
