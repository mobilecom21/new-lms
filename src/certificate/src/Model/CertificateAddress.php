<?php

namespace Certificate\Model;

use JsonSerializable;


class CertificateAddress implements JsonSerializable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var text
     */
    public $address;

    /**
     * @var text
     */
    public $address2;

    /**
     * @var int
     */
    public $district;

    /**
     * @var int
     */
    public $city;

    /**
     * @var int
     */
    public $state_id;

    /**
     * @var int
     */
    public $country_id;

    /**
     * @var int
     */
    public $postal_code;

    /**
     * @var int
     */
    public $phone;

    /**
     * @var int
     */
    public $last_update;

    /**
     * @return string
     */
    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? null);
		$this->setAddress($input['address'] ?? '');
		$this->setAddress2($input['address2'] ?? '');
		$this->setDistrict($input['district'] ?? '');
		$this->setCity($input['city'] ?? '');
		$this->SetStateId($input['state_id'] ?? '');
		$this->setCountryId($input['country_id'] ?? '');
		$this->setPostalCode($input['postal_code'] ?? '');
		$this->SetPhone($input['phone'] ?? '');
		$this->SetLastUpdate($input['last_update'] ?? '');
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
    public function setId(int $id): CertificateAddress
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setAddress(string $address): CertificateAddress
    {
        $this->address = $address;
        return $this;
    }

	/**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setAddress2(string $address2): CertificateAddress
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return string
     */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setDistrict(string $district): CertificateAddress
    {
        $this->district = $district;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setCity(string $city): CertificateAddress
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getStateId(): int
    {
        return $this->state_id;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setStateId(int $state): CertificateAddress
    {
        $this->state_id = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryId(): int
    {
        return $this->country_id;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setCountryId(int $country): CertificateAddress
    {
        $this->country_id = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setPhone(string $phone): CertificateAddress
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): int
    {
        return $this->postal_code;
    }

    /**
     * @param string $address
     * @return string
     */
    public function setPostalCode(int $postal_code): CertificateAddress
    {
        $this->postal_code= $postal_code;
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
    public function setLastUpdate(string $datetime): CertificateAddress
    {
        $this->last_update = $datetime;
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
