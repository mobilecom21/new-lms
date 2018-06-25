<?php

namespace Certificate\Model;

use JsonSerializable;


class CertificatePayment implements JsonSerializable
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
    public $exam_id;

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
        $this->setId($input['id'] ?? null);
		$this->setStudentId($input['user_id'] ?? null);
		$this->setExamId($input['exam_id'] ?? null);
		$this->setItemName($input['item_name'] ?? 'Print Certificates');
		$this->setCreationDate($input['creation_date'] ?? null);
		$this->SetAmount($input['amount'] ?? 25);
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
     * @return CertificatePayment
     */
    public function setId(int $id): CertificatePayment
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
     * @return CertificatePayment
     */
    public function setStudentId(int $id): CertificatePayment
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
     * @return CertificatePayment
     */
    public function setExamId(int $id): CertificatePayment
    {
        $this->exam_id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return $this->item_name;
    }

    /**
     * @param string $item_name
     * @return CertificatePayment
     */
    public function setItemName(string $item_name): CertificatePayment
    {
        $this->item_name = $item_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreationDate(): string
    {
        return $this->creation_date;
    }

    /**
     * @param string $datetime
     * @return CertificatePayment
     */
    public function setCreationDate(string $datetime): CertificatePayment
    {
        $this->creation_date = $datetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return CertificatePayment
     */
    public function setAmount(string $amount): CertificatePayment
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
