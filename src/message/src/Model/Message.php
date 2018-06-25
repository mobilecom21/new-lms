<?php

namespace Message\Model;

class Message
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $sender;

    /**
     * @var int
     */
    protected $receiver;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $viewed;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $hideToSender;

    /**
     * @var int
     */
    protected $hideToReceiver;

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
     * @return Message
     */
    public function setId(int $id): Message
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSender(): int
    {
        return $this->sender;
    }

    /**
     * @param int $sender
     *
     * @return Message
     */
    public function setSender(int $sender): Message
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return int
     */
    public function getReceiver(): int
    {
        return $this->receiver;
    }

    /**
     * @param int $receiver
     *
     * @return Message
     */
    public function setReceiver(int $receiver): Message
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getText(): ?\stdClass
    {
        return json_decode($this->text);
    }

    /**
     * @param string $text
     *
     * @return Message
     */
    public function setText(string $text): Message
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
     * @return Message
     */
    public function setCreatedAt(int $createdAt): Message
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getViewed(): int
    {
        return $this->viewed;
    }

    /**
     * @param int $receiver
     *
     * @return Message
     */
    public function setViewed(int $viewed): Message
    {
        $this->viewed = $viewed;
        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName): Message
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName): Message
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): Message
    {
        $this->status = $status;
        return $this;
    }

    public function getHideToSender()
    {
        return $this->hideToSender;
    }

    public function setHideToSender($hideToSender): Message
    {
        $this->hideToSender = $hideToSender;
        return $this;
    }

    public function getHideToReceiver()
    {
        return $this->hideToReceiver;
    }

    public function setHideToReceiver($hideToReceiver): Message
    {
        $this->hideToReceiver = $hideToReceiver;
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
