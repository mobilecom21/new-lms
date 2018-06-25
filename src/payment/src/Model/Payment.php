<?php

namespace Payment\Model;

use JsonSerializable;


class Payment implements JsonSerializable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $student_id;

    /**
     * @var item
     */
    public $item_name;

    /**
     * @var int
     */
    public $course_id;

    /**
     * @var int
     */
    public $creation_date;

    /**
     * @var int
     */
    public $amount;

    /**
     * @return string
     */
    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? 0);
		$this->setStudentId($input['user_id'] ?? 0);
		$this->setCourseId($input['course_id'] ?? 0);
		$this->setItemName($input['item_name'] ?? '');
		$this->setCreatedAt($input['creation_date'] ?? '');
		$this->SetAmount($input['amount'] ?? 0);
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
    public function setId(int $id): Payment
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
    public function setStudentId(int $id): Payment
    {
        $this->student_id = $id;
        return $this;
    }
    /**
     * @return int
     */
    public function getCourseId(): int
    {
        return $this->course_id;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setCourseId(int $id): Payment
    {
        $this->course_id = $id;
        return $this;
    }

    public function getItemName(): string
    {
        return $this->item_name;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setItemName(string $item_name): Payment
    {
        $this->item_name = $item_name;
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
    public function setHasDownload(int $id): Payment
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
     * @return Payment
     */
    public function setCreatedAt(string $datetime): Payment
    {
        $this->createdAt = $datetime;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setAmount(string $amount): Payment
    {
        $this->amount = $amount;
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
