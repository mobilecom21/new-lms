<?php

namespace Exam\Model;

use JsonSerializable;

class ExamTries implements JsonSerializable
{
    /**
     * @var int
     */
    protected $Id;

    /**
     * @var string
     */
    protected $studentId;

    /**
     * @var string
     */
    protected $courseId;

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? null);
		$this->setCourseId($input['course_id'] ?? null);
        $this->setStudentId($input['student_id'] ?? null);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->Id;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setId(int $Id): ExamTries
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCourseId(): int
    {
        return $this->courseId;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setCourseId(int $courseId): ExamTries
    {
        $this->courseId = $courseId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStudentId(): int
    {
        return $this->studentId;
    }

    /**
     * @param string $name
     * @return Exam
     */
    public function setStudentId(int $studentId): ExamTries
    {
        $this->studentId = $studentId;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }

    /**
     * Return array for object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
