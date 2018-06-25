<?php

namespace Course\Model;

use User\Model\UserTableGateway;
use Exam\Model\ExamTableGateway;
use Course\Model\ContentTableGateway;
use Course\Model\CourseUserTableGateway;
use Zend\Db\Sql;

class CourseUserTable
{
    /**
     * @var CourseUserTableGateway
     */
    private $tableGateway;

    /**
     * @var CourseTableGateway
     */
    private $courseTableGateway;

    /**
     * @var UserTableGateway
     */
    private $userTableGateway;

    /**
     * @var CourseUserTableGateway;
     */
    private $courseUserTableGateway;

    public function __construct(
        CourseUserTableGateway $tableGateway,
        CourseTableGateway $courseTableGateway,
        UserTableGateway $userTableGateway,
        ExamTableGateway $examTableGateway,
        ContentTableGateway $contentTableGateway,
        CourseUserTableGateway $courseUserTableGateway
    )
    {
        $this->tableGateway = $tableGateway;
        $this->courseTableGateway = $courseTableGateway;
        $this->userTableGateway = $userTableGateway;
        $this->examTableGateway = $examTableGateway;
        $this->contentTableGateway = $contentTableGateway;
        $this->courseUserTableGateway = $courseUserTableGateway;
    }

    public function oneByCourseIdAndUserId(int $courseId, int $userId)
    {
        return $this->tableGateway->select(function (Sql\Select $select) use ($courseId, $userId) {
            $ct = $this->courseTableGateway->getTable();
            $cut = $this->tableGateway->getTable();
            return $select->join($ct, $cut . '.course_id = ' . $ct . '.id', $select::SQL_STAR, $select::JOIN_LEFT)
                ->where(['course_id' => $courseId, 'user_id' => $userId]);
        })->current();
    }

    public function fetchByUserId(int $userId, int $archived = 0)
    {
        return $this->tableGateway->select(function (Sql\Select $select) use ($userId, $archived) {
            $ct = $this->courseTableGateway->getTable();
            $cut = $this->tableGateway->getTable();
            return $select->join($ct, $cut . '.course_id = ' . $ct . '.id', $select::SQL_STAR, $select::JOIN_LEFT)
                ->where(['user_id' => $userId, 'archived' => $archived]);
        });
    }

    public function fetchUserByCourseIdAndUserRole(int $courseId, string $role)
    {
        return $this->userTableGateway->select(function (Sql\Select $select) use ($courseId, $role) {
            $ut = $this->userTableGateway->getTable();
            $cut = $this->tableGateway->getTable();
            return $select->join($cut, $cut . '.user_id = ' . $ut . '.id', null, $select::JOIN_RIGHT)
                ->where(['course_id' => $courseId, 'role' => $role]);
        });
    }

    public function isUserEnrolledInShortCourse(int $userId)
    {
        return $this->examTableGateway->select(function (Sql\Select $select) use ($userId) {
            $exam = $this->examTableGateway->getTable();
            $content = $this->contentTableGateway->getTable();
            $course_user = $this->courseUserTableGateway->getTable();

            return $select->join(
                    $content, $content . '.content_id = ' . $exam . '.topic_id', $select::SQL_STAR, $select::JOIN_LEFT
                )->join(
                    $course_user, $course_user . '.course_id = ' . $content . '.course_id', $select::SQL_STAR, $select::JOIN_LEFT
                )->where(['course_user.user_id' => $userId]);
        });

    }



    public function save(int $userId, int ...$courseIds)
    {
        $this->tableGateway->delete(['user_id' => $userId]);

        foreach ($courseIds as $courseId) {
            $this->insert($courseId, $userId);
        }

        return count($courseIds);
    }

    protected function insert(int $courseId, int $userId): int
    {
        $this->tableGateway->insert([
            'course_id' => $courseId,
            'user_id' => $userId
        ]);

        return $this->tableGateway->lastInsertValue;
    }
}
