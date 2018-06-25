<?php

namespace File\Model;

use Topic\Model\AttachmentTable as TopicAttachmentTable;
use Zend\Db\ResultSet\ResultSet;

class FileTable
{
    /**
     * @var FileTableGateway
     */
    private $tableGateway;

    /**
     * @var TopicAttachmentTable
     */
    private $topicAttachmentTable;

    public function __construct(FileTableGateway $tableGateway, TopicAttachmentTable $topicAttachmentTable)
    {
        $this->tableGateway = $tableGateway;
        $this->topicAttachmentTable = $topicAttachmentTable;
    }

    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function save(array $data): int
    {
        if (!empty($data['id'])) {
            return $this->tableGateway->update([
                'name' => $data['name'],
                'uploads' => $data['uploads']
            ], ['id' => $data['id']]);
        }

        $this->tableGateway->insert([
            'name' => $data['name'],
            'uploads' => $data['uploads']
        ]);

        $lastInsertValue = $this->tableGateway->lastInsertValue;

        // link file with topic
        $this->topicAttachmentTable->add($data['parentId'], 'File', $lastInsertValue);

        return $lastInsertValue;
    }

    public function delete(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
        $this->topicAttachmentTable->delete('File', $id);
    }
}
