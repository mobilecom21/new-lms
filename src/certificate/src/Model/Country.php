<?php

namespace Certificate\Model;

use JsonSerializable;


class Country implements JsonSerializable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var name
     */
    public $sortname;

    /**
     * @var name
     */
    public $name;

    /**
     * @var phone
     */
    public $phonecode;

    /**
     * @return string
     */
    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? '');
		$this->setSortName($input['sortname'] ?? '');
		$this->setName($input['name'] ?? '');
		$this->setPhoneCode($input['phonecode'] ?? '');
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
    public function setId(int $id): Country
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @return string
     */
    public function getSortName(): string
    {
        return $this->sortname;
    }

    /**
     * @param string
     * @return Country
     */
    public function setSortName(string $sortname): Country
    {
        $this->sortname = $sortname;
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
    public function setName(string $name): Country
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneCode(): string
    {
        return $this->phonecode;
    }

    /**
     * @param string
     * @return Country
     */
    public function setPhoneCode(string $phonecode): Country
    {
        $this->phonecode = $phonecode;
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
