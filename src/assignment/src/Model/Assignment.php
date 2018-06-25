<?php

namespace Assignment\Model;

class Assignment
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
    protected $grade;

    /**
     * @var string
     */
    protected $uploads;

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id'] ?? null);
        $this->setName($input['name'] ?? null);
        $this->setSummary($input['summary'] ?? null);
        $this->setGrade($input['grade'] ?? null);
        $this->setUploads($input['uploads'] ?? '');
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
     * @return Assignment
     */
    public function setId(int $id): Assignment
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
     * @return Assignment
     */
    public function setName(string $name): Assignment
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
     * @return Assignment
     */
    public function setSummary(string $summary): Assignment
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return int
     */
    public function getGrade(): int
    {
        return $this->grade;
    }

    /**
     * @param int $grade
     * @return Assignment
     */
    public function setGrade(int $grade): Assignment
    {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploads(): string
    {
        return $this->uploads;
    }

    /**
     * @param string $uploads
     * @return Assignment
     */
    public function setUploads(string $uploads): Assignment
    {
        $this->uploads = $uploads;
        return $this;
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
