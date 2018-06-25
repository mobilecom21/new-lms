<?php

namespace Exam\Model;

use Topic\Model\AttachmentTable as TopicAttachmentTable;
use Exam\Model\ExamTableGateway;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class ExamTable
{
    /**
     * @var ExamTableGateway
     */
    private $tableGateway;

    /**
     * @var ContentTableGateway
     */
    private $contenttableGateway;

    /**
     * @var TopicAttachmentTable
     */
    private $topicAttachmentTable;

    public function __construct(ExamTableGateway $tableGateway, ContentTableGateway $contenttableGateway, TopicAttachmentTable $topicAttachmentTable)
    {
        $this->tableGateway = $tableGateway;
        $this->contenttableGateway = $contenttableGateway;
        $this->topicAttachmentTable = $topicAttachmentTable;
    }

    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function fetchByTopicId(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['topic_id' => $ids]);
    }

    public function GetCourseIdfromExamId($examid) {
        $select = $this->tableGateway->select(['id' => $examid])->current();
        if(count($select) > 0) {
            $topicid = $select->getTopicId();
            $selectcourse = $this->contenttableGateway->select(['content_id' => $topicid])->current();
            $courseid = $selectcourse->getCourseId();
            return $courseid;
        } else {
            return false;
        }
    }

    public function save(array $data): int
    {
        if (!empty($data['id'])) {
            return $this->tableGateway->update([
                'name' => $data['name'],
                'topic_id' => $data['parentId'],
                'admin_id' => $data['adminId'],
                'filename' => $data['name'],
                'uploads' => $data['uploads']
            ], ['id' => $data['id']]);
        }

        $this->tableGateway->insert([
            'name' => $data['name'],
            'topic_id' => $data['parentId'],
            'admin_id' => $data['adminId'],
            'filename' => $data['name'],
            'uploads' => $data['uploads']
        ]);

        $lastInsertValue = $this->tableGateway->lastInsertValue;

        // link file with topic
        $this->topicAttachmentTable->add($data['parentId'], 'Exam', $lastInsertValue);

        return $lastInsertValue;
    }

    public function delete(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
        $this->topicAttachmentTable->delete('Exam', $id);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }
}