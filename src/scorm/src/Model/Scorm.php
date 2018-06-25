<?php

namespace Scorm\Model;

class Scorm
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
        $this->setSummary($input['summary'] ?? '');
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
     * @return Scorm
     */
    public function setId(int $id): Scorm
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
     * @return Scorm
     */
    public function setName(string $name): Scorm
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
     * @return Scorm
     */
    public function setSummary(string $summary): Scorm
    {
        $this->summary = $summary;
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
     * @return Scorm
     */
    public function setUploads(string $uploads): Scorm
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
