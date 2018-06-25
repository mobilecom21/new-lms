<?php

namespace Topic\Model;

use JsonSerializable;

class Topic implements JsonSerializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var int
     */
    protected $sort_order;

    /**
     * @var int
     */
    protected $required;

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? null);
        $this->setName($input['name'] ?? null);
        $this->setSummary($input['summary'] ?? null);
        $this->setRequired($input['required'] ?? null);
        $this->setSort($input['sort_order'] ?? null);
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
     * @return Topic
     */
    public function setId(int $id): Topic
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
     * @param string $name
     * @return Topic
     */
    public function setName(string $name): Topic
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return Topic
     */
    public function setSummary(string $summary): Topic
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return int
     */
    public function getRequired(): int
    {
        return $this->required;
    }

    /**
     * @param int $required
     * @return Topic
     */
    public function setRequired(int $required): Topic
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort_order;
    }


    /**
     * @param int $sort_order
     * @return Topic
     */
    public function setSort(? int $sort_order): Topic
    {
        $this->sort_order = $sort_order;
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
