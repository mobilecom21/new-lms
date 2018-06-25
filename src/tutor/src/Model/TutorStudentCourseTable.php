<?php

namespace Tutor\Model;

use Zend\Db\ResultSet\AbstractResultSet;

class TutorStudentCourseTable
{
    /**
     * @var TutorStudentCourseTableGateway
     */
    private $tutorStudentCourseTableGateway;

    public function __construct(TutorStudentCourseTableGateway $tutorStudentCourseTableGateway)
    {
        $this->tutorStudentCourseTableGateway = $tutorStudentCourseTableGateway;
    }

    public function fetchByStudentId(int $studentId): ?AbstractResultSet
    {
        return $this->tutorStudentCourseTableGateway->select(['student_id' => $studentId]);
    }

    public function fetchByCourseId(int $courseId): ?AbstractResultSet
    {
        return $this->tutorStudentCourseTableGateway->select(['course_id' => $courseId]);
    }

    public function fetchByStudentAndTutor(int $studentId, int $tutorId): ?AbstractResultSet
    {
        return $this->tutorStudentCourseTableGateway->select(['student_id' => $studentId, 'tutor_id' => $tutorId]);
    }

    public function fetchByStudentAndCourse(int $studentId, int $courseId): ?AbstractResultSet
    {
        return $this->tutorStudentCourseTableGateway->select(['student_id' => $studentId, 'course_id' => $courseId]);
    }

    public function fetchTutorForStudentAndCourse(int $studentId, int $courseId)
    {
        return $this->tutorStudentCourseTableGateway->select([
            'student_id' => $studentId,
            'course_id' => $courseId
        ])->current();
    }

    public function fetchByStudentAndTutorAndCourse(int $studentId, int $tutorId, int $courseId)
    {
        return $this->tutorStudentCourseTableGateway->select([
            'student_id' => $studentId,
            'tutor_id' => $tutorId,
            'course_id' => $courseId
        ])->current();
    }

    public function isTutorForStudent(int $tutorId, int $studentId)
    {
        return $this->tutorStudentCourseTableGateway->select([
            'tutor_id' => $tutorId,
            'student_id' => $studentId
        ])->current();
    }

    public function isStudentAllowed(int $studentId, int $courseId)
    {
        return  $this->tutorStudentCourseTableGateway->select([
            'student_id' => $studentId,
            'course_id' => $courseId
        ])->current();
    }

    public function isTutorAllowed(int $tutorId, int $courseId)
    {
        return $this->tutorStudentCourseTableGateway->select([
            'tutor_id' => $tutorId,
            'course_id' => $courseId
        ])->current();
    }

    public function save($studentId, array ...$coursesAndTutors)
    {
        $this->tutorStudentCourseTableGateway->delete([
            'student_id' => $studentId
        ]);

        foreach ($coursesAndTutors as $courseAndTutor) {
            $this->insert(
                $courseAndTutor['tutor'],
                $studentId,
                $courseAndTutor['course'],
                strtotime($courseAndTutor['end_of_support']),
                $courseAndTutor['home_learning_number'],
                $courseAndTutor['order_number']
            );
        }

        return count($coursesAndTutors);
    }

    protected function insert(
        int $tutorId,
        int $studentId,
        int $courseId,
        int $endOfSupport,
        string $homeLearningNumber,
        string $orderNumber
    )
    {
        $this->tutorStudentCourseTableGateway->insert([
            'tutor_id' => $tutorId,
            'student_id' => $studentId,
            'course_id' => $courseId,
            'end_of_support' => $endOfSupport,
            'home_learning_number' => $homeLearningNumber,
            'order_number' => $orderNumber
        ]);

        return $this->tutorStudentCourseTableGateway->lastInsertValue;
    }
}
