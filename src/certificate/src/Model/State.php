<?php

namespace Certificate\Model;

use JsonSerializable;


class State implements JsonSerializable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var name
     */
    public $name;

    /**
     * @var int
     */
    public $country_id;

    /**
     * @return string
     */
    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id']);
		$this->setName($input['name']);
		$this->setCountryId($input['country_id']);
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
    public function setId(int $id): State
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string
     * @return Country
     */
    public function setName(string $name): State
    {
        $this->name = $name;
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
     * @param string
     * @return Country
     */
    public function setCountryId(int $country_id): State
    {
        $this->country_id = $country_id;
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
