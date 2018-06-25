<?php

namespace Assignment\Model;

use Course\Model\Course;
use Topic\Model\Topic;
use Uploader\Model\Uploader;
use User\Model\User;

class AssignmentWork
{
    CONST STATUS_WAIT = 1000;
    CONST STATUS_FAIL = 2000;
    CONST STATUS_PASS = 3000;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Assignment
     */
    protected $assignment;

    /**
     * @var User
     */
    protected $worker;

    /**
     * @var User
     */
    protected $tutor;

    /**
     * @var Uploader
     */
    protected $assignmentUploader;

    /**
     * @var Uploader
     */
    protected $feedbackUploader;

    /**
     * @var Course
     */
    private $course;

    /**
     * @var Topic
     */
    private $topic;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $updatedAt;

    /**
     * @var int
     */
    private $markingDaysLeft;

    public function __construct(
        int $id,
        Assignment $assignment,
        User $worker,
        User $tutor,
        Uploader $assignmentUploader,
        ?Uploader $feedbackUploader,
        Course $course,
        Topic $topic,
        int $status,
        int $createdAt,
        ?int $updatedAt,
        $markingDaysLeft
    ) {
        $this->id = $id;
        $this->assignment = $assignment;
        $this->worker = $worker;
        $this->tutor = $tutor;
        $this->assignmentUploader = $assignmentUploader;
        $this->feedbackUploader = $feedbackUploader;
        $this->course = $course;
        $this->topic = $topic;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->markingDaysLeft = $markingDaysLeft;
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Assignment
     */
    public function assignment(): Assignment
    {
        return $this->assignment;
    }

    /**
     * @return User
     */
    public function worker(): User
    {
        return $this->worker;
    }

    /**
     * @return User
     */
    public function tutor(): User
    {
        return $this->tutor;
    }

    /**
     * @return Uploader
     */
    public function assignmentUploader(): Uploader
    {
        return $this->assignmentUploader;
    }

    /**
     * @return Uploader
     */
    public function feedbackUploader(): ?Uploader
    {
        return $this->feedbackUploader;
    }

    /**
     * @return Course
     */
    public function course(): Course
    {
        return $this->course;
    }

    /**
     * @return Topic
     */
    public function topic(): Topic
    {
        return $this->topic;
    }

    /**
     * @return bool
     */
    public function isWaitingToPass(): bool
    {
        return self::STATUS_WAIT === $this->status;
    }

    public function hasTutorFeedback(): bool
    {
        return !empty($this->updatedAt) && !$this->isWaitingToPass();
    }

    /**
     * @return bool
     */
    public function didNotPass(): bool
    {
        return self::STATUS_FAIL === $this->status;
    }

    /**
     * @return bool
     */
    public function hasPassed(): bool
    {
        return self::STATUS_PASS === $this->status;
    }

    /**
     * @return \DateTime
     */
    public function createdAt(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->createdAt);
        return $dateTime;
    }

    /**
     * @return \DateTime
     */
    public function updatedAt(): \DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->updatedAt);
        return $dateTime;
    }

    /**
     * @return int
     */
    public function markingDaysLeft()#: int
    {
        return $this->markingDaysLeft;
    }
}
