<?php

namespace Tutor\Model;

class TutorStudentCourse
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $tutorId;

    /**
     * @var int
     */
    protected $studentId;

    /**
     * @var int
     */
    protected $courseId;

    /**
     * @var int
     */
    protected $endOfSupport;

    /**
     * @var string
     */
    protected $homeLearningNumber;

    /**
     * @var string
     */
    protected $orderNumber;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return TutorStudentCourse
     */
    public function setId(int $id): TutorStudentCourse
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTutorId(): int
    {
        return $this->tutorId;
    }

    /**
     * @param int $tutorId
     *
     * @return TutorStudentCourse
     */
    public function setTutorId(int $tutorId): TutorStudentCourse
    {
        $this->tutorId = $tutorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getStudentId(): int
    {
        return $this->studentId;
    }

    /**
     * @param int $studentId
     *
     * @return TutorStudentCourse
     */
    public function setStudentId(int $studentId): TutorStudentCourse
    {
        $this->studentId = $studentId;
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
     * @param int $courseId
     *
     * @return TutorStudentCourse
     */
    public function setCourseId(int $courseId): TutorStudentCourse
    {
        $this->courseId = $courseId;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndOfSupport(): ?int
    {
        return $this->endOfSupport;
    }

    /**
     * @param int $endOfSupport
     *
     * @return TutorStudentCourse
     */
    public function setEndOfSupport(?int $endOfSupport): TutorStudentCourse
    {
        $this->endOfSupport = $endOfSupport;
        return $this;
    }

    /**
     * @return string
     */
    public function getHomeLearningNumber(): ?string
    {
        return $this->homeLearningNumber;
    }

    /**
     * @param string $homeLearningNumber
     *
     * @return TutorStudentCourse
     */
    public function setHomeLearningNumber(?string $homeLearningNumber): TutorStudentCourse
    {
        $this->homeLearningNumber = $homeLearningNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     *
     * @return TutorStudentCourse
     */
    public function setOrderNumber(?string $orderNumber): TutorStudentCourse
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }
}
