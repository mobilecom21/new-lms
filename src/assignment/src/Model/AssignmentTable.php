<?php

namespace Assignment\Model;

use Topic\Model\AttachmentTable as TopicAttachmentTable;
use Assignment\Model\AssignmentWorkTableGateway;

class AssignmentTable
{
    /**
     * @var AssignmentTableGateway
     */
    private $tableGateway;

    /**
     * @var AssignmentWorkTableGateway
     */
    private $assignmentWorkTableGateway;

    /**
     * @var TopicAttachmentTable
     */
    private $topicAttachmentTable;

    public function __construct(
        AssignmentTableGateway $tableGateway,
        AssignmentWorkTableGateway $assignmentWorkTableGateway,
        TopicAttachmentTable $topicAttachmentTable
    )
    {
        $this->tableGateway = $tableGateway;
        $this->assignmentWorkTableGateway = $assignmentWorkTableGateway;
        $this->topicAttachmentTable = $topicAttachmentTable;
    }

    public function fetchById(int $id)
    {
        return $this->tableGateway->select(['id' => $id])->current();
    }

    public function save(array $data): int
    {
        if (!empty($data['id'])) {
            return $this->tableGateway->update([
                'name' => $data['name'],
                'summary' => $data['summary'],
                'grade' => $data['grade'],
                'uploads' => $data['uploads'],
            ], ['id' => $data['id']]);
        }

        $this->tableGateway->insert([
            'name' => $data['name'],
            'summary' => $data['summary'],
            'grade' => $data['grade'],
            'uploads' => $data['uploads']
        ]);

        $lastInsertValue = $this->tableGateway->lastInsertValue;

        // link assignment with topic
        $this->topicAttachmentTable->add($data['parentId'], 'Assignment', $lastInsertValue);

        return $lastInsertValue;
    }

    public function unlock(int $id): void
    {
        $this->assignmentWorkTableGateway->update([
            'status' => '1000',
        ], ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->assignmentWorkTableGateway->delete(['assignment' => $id]);
        $this->tableGateway->delete(['id' => $id]);
        $this->topicAttachmentTable->delete('Assignment', $id);
    }
}
