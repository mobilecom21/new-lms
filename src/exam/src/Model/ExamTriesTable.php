<?php

namespace Exam\Model;

use Exam;
use Content;
use Exam\Model\ExamTableGateway;
use Exam\Model\ExamTriesTableGateway;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class ExamTriesTable
{
    /**
     * @var ExamTriesTableGateway
     */
    private $tableGateway;

    /**
     * @var ContentTableGateway
     */
    private $contenttableGateway;

    /**
     * @var ExamTableGateway
     */
    private $examtableGateway;

    public function __construct(ExamTriesTableGateway $tableGateway, ExamTableGateway $examtableGateway, ContentTableGateway $contenttableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->contenttableGateway = $contenttableGateway;
        $this->examtableGateway = $examtableGateway;
    }

    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function GetCourseIdfromExamId($examid) {
        $select = $this->examtableGateway->select(['id' => $examid])->current();
        if(count($select) > 0) {
            $topicid = $select->getTopicId();
            $selectcourse = $this->contenttableGateway->select(['content_id' => $topicid])->current();
            $courseid = $selectcourse->getCourseId();
            return $courseid;
        } else {
            return false;
        }
    }

    public function GetTopicIdfromCourseId($courseid) {

        $listtopic = array();
        $select = $this->contenttableGateway->select(['course_id' => $courseid]);
        foreach ($select as $key => $topic) {
            $listtopic[$key] = $topic->getContentId();
        }
        return $listtopic;
    }

    public function getExamByCourseId($courseid)
    {
        $listtopic = $this->GetTopicIdfromCourseId($courseid);
        //var_dump($listtopic);
        $listexamid = array();

        $row= 0;
        if (count($listtopic) > 0) {
            foreach ($listtopic as $key => $topicid) {
                $list = $this->examtableGateway->select(['topic_id' => $topicid]);
                if (count($list) > 0) {
                    foreach ($list as $exam) {
                        $listexamid[$row] = $exam->getId();
                        $row++;
                    }
                }
            }
            return $listexamid;
        } else {
            return array();
        }
    }
    public function isCourseNoLimit(int $course_id, int $student_id) {
        $select = $this->tableGateway->getSql()->select()->where([
            'student_id = ?' => $student_id,
            'course_id = ?' => $course_id,
        ]);
        $count = count($this->tableGateway->selectWith($select));

        if ($count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function isExamNoLimit(int $exam_id, int $student_id) {
        $course_id = GetCourseIdfromExamId($exam_id);
        return $this->isCourseNoLimit($course_id, $student_id);
    }

    public function fetchExamByCourseId($courseid)
    {
        $listtopic = $this->GetTopicIdfromCourseId($courseid);
        //var_dump($listtopic);
        $listexamid = array();

        $row= 0;
        if (count($listtopic) > 0) {
            foreach ($listtopic as $key => $topicid) {
                $list = $this->examtableGateway->select(['topic_id' => $topicid]);
                if (count($list) > 0) {
                    foreach ($list as $exam) {
                        $listexamid[$row] = $exam;
                        $row++;
                    }
                }
            }
            return $listexamid;
        } else {
            return array();
        }
    }

    public function save(int $userId, int ...$courseIds)
    {
        $this->tableGateway->delete(['student_id' => $userId]);

        foreach ($courseIds as $courseId) {
            $this->insert($courseId, $userId);
        }

        return count($courseIds);
    }

    public function save_single(int $userId, int $courseId)
    {
        $this->tableGateway->delete(['student_id' => $userId,'course_id' => $courseId]);

        $this->insert($courseId, $userId);

        return $this->tableGateway->lastInsertValue;
    }

    protected function insert(int $courseId, int $userId): int
    {
        $this->tableGateway->insert([
            'course_id' => $courseId,
            'student_id' => $userId
        ]);

        return $this->tableGateway->lastInsertValue;
    }

    public function delete(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }
}