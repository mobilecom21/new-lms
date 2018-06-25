<?php

namespace Attempt\Model;

use JsonSerializable;


class Attempt implements JsonSerializable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $exam_id;

    /**
     * @var int
     */
    public $student_id;

    /**
     * @var string
     */
    protected $attempted_answer;

    /**
     * @var string
     */
    protected $expected_answer;

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var int
     */
    public $hasdownload;

    /**
     * @return string
     */
    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? null);
        $this->setStudentId($input['student_id'] ?? null);
        $this->setExamId($input['exam_id'] ?? null);
        $this->setScore($input['score'] ?? 0);
        $this->setHasDownload($input['download'] ?? 0);
        $this->SetCreatedAt($input['datetime'] ?? null);
    }
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setId(int $id): Attempt
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @return int
     */
    public function getStudentId(): int
    {
        return $this->student_id;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setStudentId(int $id): Attempt
    {
        $this->student_id = $id;
        return $this;
    }
    /**
     * @return int
     */
    public function getExamId(): int
    {
        return $this->exam_id;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setExamId(int $id): Attempt
    {
        $this->exam_id = $id;
        return $this;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setScore(float $score): Attempt
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return int
     */
    public function getHasDownload(): int
    {
        return $this->hasdownload;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setHasDownload(int $id): Attempt
    {
        $this->hasdownload = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setCreatedAt(string $datetime): Attempt
    {
        $this->createdAt = $datetime;
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
