<?php

namespace Certificate\Model;

use JsonSerializable;


class CertificateDelivery implements JsonSerializable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $payment_id;

    /**
     * @var Status Sent
     */
    public $status_sent;

    /**
     * @var Last Update
     */
    public $last_update;

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id']);
		$this->setPaymentId($input['payment_id']);
		$this->setStatusSent($input['status_sent'] ?? 0);
		$this->setLastUpdate($input['last_update']);
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
    public function setId(int $id): CertificateDelivery
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentId(): int
    {
        return $this->payment_id;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setPaymentId(int $payment_id): CertificateDelivery
    {
        $this->payment_id = $payment_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusSent(): int
    {
        return $this->status_sent;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function setStatusSent(int $status_sent): CertificateDelivery
    {
        $this->status_sent = $status_sent;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastUpdate(): string
    {
        return $this->last_update;
    }

    /**
     * @param int $id
     * @return Payment
     */
    public function setLastUpdate(string $datetime): CertificateDelivery
    {
        $this->last_update = $datetime;
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
