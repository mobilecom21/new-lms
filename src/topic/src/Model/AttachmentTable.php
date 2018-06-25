<?php

namespace Topic\Model;

use Zend\Db\Sql\Where;

class AttachmentTable
{
    /**
     * @var AttachmentTableGateway
     */
    private $tableGateway;

    public function __construct(AttachmentTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchByTopicId(int ...$topicId)
    {
        //return $this->tableGateway->select(function (Select $select) {$select->order('id DESC');});

        return $this->tableGateway->select(['topic_id' => $topicId]);

        return $this->tableGateway->select(function (Select $select) { $select->where(['topic_id' => $topicId]); $select->order('sort_order ASC');});
    }

    public function fetchByAttachmentAndAttachmentId(int $id, string $attachment)
    {
        return $this->tableGateway->select(['attachment' => $attachment, 'attachment_id' => $id]);
    }

    public function fetchByAttachmentAndTopicId(string $attachment, int $topicId)
    {
        return $this->tableGateway->select(['attachment' => $attachment, 'topic_id' => $topicId]);
    }

    public function oneAssignmentById(int $id)
    {
        return $this->tableGateway->select([
            'attachment' => 'Assignment',
            'attachment_id' => $id
        ])->current();
    }

    public function add(int $topicId, string $attachment, int $attachmentId): int
    {
        $this->tableGateway->insert([
            'topic_id' => $topicId,
            'attachment' => $attachment,
            'attachment_id' => $attachmentId
        ]);

        return $this->tableGateway->lastInsertValue;
    }

    public function delete(string $attachment, int $attachmentId): int
    {
        return $this->tableGateway->delete([
            'attachment' => $attachment,
            'attachment_id' => $attachmentId
        ]);
    }
}
