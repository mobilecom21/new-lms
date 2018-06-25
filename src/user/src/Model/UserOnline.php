<?php

namespace User\Model;

class UserOnline
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
     * @return UserOnline
     */
    public function setId(int $id): UserOnline
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
     * @return UserOnline
     */
    public function setUserId(int $userId): UserOnline
    {
        $this->userId = $userId;
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
     * @return UserOnline
     */
    public function setCreatedAt(int $createdAt): UserOnline
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
