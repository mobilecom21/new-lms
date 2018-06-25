<?php

namespace User\Model;

class UserLogin
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
    protected $ip;

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
     * @return UserLogin
     */
    public function setId(int $id): UserLogin
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
     * @return UserLogin
     */
    public function setUserId(int $userId): UserLogin
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     *
     * @return UserLogin
     */
    public function setIp(string $ip): UserLogin
    {
        $this->ip = $ip;
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
     * @return UserLogin
     */
    public function setCreatedAt(int $createdAt): UserLogin
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
