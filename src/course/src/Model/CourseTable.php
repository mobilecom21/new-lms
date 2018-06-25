<?php

namespace Course\Model;

use Course\Model\ContentTableGateway;
use Course\Model\CourseUserTableGateway;
use Tutor\Model\TutorStudentCourseTableGateway;

class CourseTable
{
    /**
     * @var CourseTableGateway
     */
    private $tableGateway;

    /**
     * @var ContentTableGateway
     */
    private $contentTableGateway;

    /**
     * @var CourseUserTableGateway
     */
    private $courseUserTableGateway;

    /**
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTableGateway;

    public function __construct(
        CourseTableGateway $tableGateway,
        ContentTableGateway $contentTableGateway,
        CourseUserTableGateway $courseUserTableGateway,
        TutorStudentCourseTableGateway $tutorStudentCourseTableGateway
    )
    {
        $this->tableGateway = $tableGateway;
        $this->contentTableGateway = $contentTableGateway;
        $this->courseUserTableGateway = $courseUserTableGateway;
        $this->tutorStudentCourseTableGateway = $tutorStudentCourseTableGateway;
    }

    public function fetchById(int $id)
    {
        return $this->tableGateway->select(['id' => $id])->current();
    }

    public function fetchByFilter(int $archived = 0)
    {
        return $this->tableGateway->select(['archived' => $archived]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function save(array $data): int
    {
        if (!empty($data['id'])) {
            $this->tableGateway->update([
                'name' => $data['name'],
                'summary' => $data['summary']
            ], ['id' => $data['id']]);
            return $data['id'];
        }

        $this->tableGateway->insert([
            'name' => $data['name'],
            'summary' => $data['summary']
        ]);

        return $this->tableGateway->lastInsertValue;
    }

    public function archive(int $id): void
    {
        $this->tableGateway->update([
            'archived' => 1
        ], ['id' => $id]);
    }

    public function restore(int $id): void
    {
        $this->tableGateway->update([
            'archived' => 0
        ], ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->courseUserTableGateway->delete(['course_id' => $id]);
        $this->tutorStudentCourseTableGateway->delete(['course_id' => $id]);
        $this->contentTableGateway->delete(['course_id' => $id]);
        $this->tableGateway->delete(['id' => $id]);
    }
}
