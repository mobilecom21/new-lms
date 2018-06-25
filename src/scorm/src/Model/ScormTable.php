<?php

namespace Scorm\Model;

use Topic\Model\AttachmentTable as TopicAttachmentTable;
use Zend\Db\ResultSet\ResultSet;

class ScormTable
{
    /**
     * @var ScormTableGateway
     */
    private $tableGateway;

    /**
     * @var TopicAttachmentTable
     */
    private $topicAttachmentTable;

    public function __construct(ScormTableGateway $tableGateway, TopicAttachmentTable $topicAttachmentTable)
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
                'summary' => $data['summary'],
                'uploads' => $data['uploads']
            ], ['id' => $data['id']]);
        }

        $this->tableGateway->insert([
            'name' => $data['name'],
            'summary' => $data['summary'],
            'uploads' => $data['uploads']
        ]);

        $lastInsertValue = $this->tableGateway->lastInsertValue;

        // link scorm with topic
        $this->topicAttachmentTable->add($data['parentId'], 'Scorm', $lastInsertValue);

        return $lastInsertValue;
    }

    public function delete(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
        $this->topicAttachmentTable->delete('Scorm', $id);
    }
}
