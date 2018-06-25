<?php

namespace Exam\Model;

use JsonSerializable;

class Exam implements JsonSerializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $topicid;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $name;

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
		$this->setTopicId($input['topic_id'] ?? null);
        $this->setName($input['name'] ?? null);
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
     * @return Exam
     */
    public function setId(int $id): Exam
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTopicId(): string
    {
        return $this->topicid;
    }

    /**
     * @param string $name
     * @return Exam
     */
    public function setTopicId(string $id): Exam
    {
        $this->topicid = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTopicName(): string
    {
        return $this->topicname;
    }

    /**
     * @param string $name
     * @return Exam
     */
    public function setTopicName(string $name): Exam
    {
        $this->topicname = $name;
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
     * @param string $summary
     * @return Exam
     */
    public function setName(string $name): Exam
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->filename;
    }

    /**
     * @param string $summary
     * @return Exam
     */
    public function setFileName(string $name): Exam
    {
        $this->filename = $name;
        return $this;
    }

    public function getUploads(): string
    {
        return $this->uploads;
    }

    /**
     * @param string $uploads
     * @return File
     */
    public function setUploads(string $uploads): Exam
    {
        $this->uploads = $uploads;
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
