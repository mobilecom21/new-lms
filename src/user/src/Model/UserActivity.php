<?php

namespace User\Model;

class UserActivity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return UserActivity
     */
    public function setId(int $id): UserActivity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return UserActivity
     */
    public function setUserId(int $userId): UserActivity
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getText(): \stdClass
    {
        return json_decode($this->text);
    }

    /**
     * @param string $text
     *
     * @return UserActivity
     */
    public function setText(string $text): UserActivity
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->createdAt);
        return $dateTime;
    }

    /**
     * @param int $createdAt
     *
     * @return UserActivity
     */
    public function setCreatedAt(int $createdAt): UserActivity
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
