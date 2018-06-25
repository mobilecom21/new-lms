<?php

namespace Course\Model;

use Topic\Model\TopicTableGateway;
use Zend\Db\Sql\Select;

class ContentTable
{
    /**
     * @var ContentTableGateway
     */
    private $tableGateway;

    /**
     * @var TopicTableGateway
     */
    private $topicTableGateway;

    public function __construct(ContentTableGateway $tableGateway, TopicTableGateway $topicTableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->topicTableGateway = $topicTableGateway;
    }

    public function fetchByCourseId(int ...$courseId)
    {
        return $this->tableGateway->select(['course_id' => $courseId]);
    }

    public function fetchByContentId(int ...$contentId)
    {
        return $this->tableGateway->select(['content_id' => $contentId]);
    }

    public function fetchByContentAndContentId(int $contentId, string $content)
    {
        return $this->tableGateway->select(['content' => $content, 'content_id' => $contentId]);
    }

    public function oneByContentId(int $contentId)
    {
        return $this->tableGateway
            ->select(['content_id' => $contentId])
            ->current();
    }

    public function contentByCourseId(int $courseId)
    {
        $table = $this->tableGateway->getTable();
        $join = $this->topicTableGateway->getTable();

        $select = (new Select($table))->where(['course_id' => $courseId]);
        $select->join($join, $table . '.content_id = ' . $join . '.id');

        $statement = $this->tableGateway->getSql()
            ->prepareStatementForSqlObject($select);

        $resultSet = $this->topicTableGateway->getResultSetPrototype();
        return $resultSet->initialize($statement->execute());
    }

    public function add(int $courseId, string $content, int $contentId): int
    {
        $this->tableGateway->insert([
            'course_id' => $courseId,
            'content' => $content,
            'content_id' => $contentId
        ]);

        return $this->tableGateway->lastInsertValue;
    }
}
