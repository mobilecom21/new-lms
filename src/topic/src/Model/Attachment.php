<?php

namespace Topic\Model;

class Attachment
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $topicId;

    /**
     * @var string
     */
    protected $attachment;

    /**
     * @var int
     */
    protected $attachmentId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Attachment
     */
    public function setId(int $id): Attachment
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTopicId(): int
    {
        return $this->topicId;
    }

    /**
     * @param int $topicId
     * @return Attachment
     */
    public function setTopicId(int $topicId): Attachment
    {
        $this->topicId = $topicId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttachment(): string
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     * @return Attachment
     */
    public function setAttachment(string $attachment): Attachment
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttachmentId(): int
    {
        return $this->attachmentId;
    }

    /**
     * @param int $attachmentId
     * @return Attachment
     */
    public function setAttachmentId(int $attachmentId): Attachment
    {
        $this->attachmentId = $attachmentId;
        return $this;
    }

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id']);
        $this->setTopicId($input['topic_id']);
        $this->setAttachment($input['attachment']);
        $this->setAttachmentId($input['attachment_id']);
    }
}
