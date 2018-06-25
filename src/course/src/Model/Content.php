<?php

namespace Course\Model;

class Content
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $courseId;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $contentId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Content
     */
    public function setId(int $id): Content
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCourseId(): int
    {
        return $this->courseId;
    }

    /**
     * @param int $courseId
     * @return Content
     */
    public function setCourseId(int $courseId): Content
    {
        $this->courseId = $courseId;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Content
     */
    public function setContent(string $content): Content
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getContentId(): int
    {
        return $this->contentId;
    }

    /**
     * @param int $contentId
     * @return Content
     */
    public function setContentId(int $contentId): Content
    {
        $this->contentId = $contentId;
        return $this;
    }

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        $this->setId($input['id']);
        $this->setCourseId($input['course_id']);
        $this->setContent($input['content']);
        $this->setContentId($input['content_id']);
    }
}
