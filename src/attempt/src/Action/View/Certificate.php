<?php

namespace Attempt\Action\View;

use Attempt\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use User\Model\UserMetaTable;
use User\Model\User;
use Exam\Model\ExamTable;
use Course\Model\CourseTable;
use Topic\Model\TopicTable;
use Course\Model\ContentTable;

class Certificate
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\AttemptTable
     */
    private $attemptTable;

    /**
     * @var Model\UserMetaTable
     */
    private $usermetaTable;

    /**
     * @var Model\ExamTable
     */
    private $examTable;

    /**
     * @var Model\courseTable
     */
    private $courseTable;

    /**
     * @var Model\topicTable
     */
    private $topicTable;

    /**
     * @var Model\contentTable
     */
    private $contentTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\AttemptTable $attemptTable, UserMetaTable $usermetaTable, ExamTable $examTable, CourseTable $courseTable, TopicTable $topicTable, ContentTable $contentTable)
    {
        $this->template = $template;
        $this->attemptTable = $attemptTable;
        $this->usermetaTable = $usermetaTable;
        $this->examTable = $examTable;
        $this->courseTable = $courseTable;
        $this->topicTable = $topicTable;
        $this->contentTable = $contentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $request->getAttribute(User::class);
        $currentUserId = $currentUser->getId();

        $id = $request->getAttribute('id');

        $attempts = $this->attemptTable->fetchById($id)->current();

        $score = 0;
        if($attempts) {
            $score = $attempts->getScore();
            if ($attempts->getStudentId() != $currentUserId) {
                return new HtmlResponse($this->template->render('error::404'), 404);
            }
        }

        $exam = $this->examTable->fetchById($attempts->getExamId())->current();
        if($exam) {
            $topic = $this->topicTable->fetchById($exam->getTopicId())->current();
            $content = $this->contentTable->fetchByContentId($topic->getId())->current();
            $course = $this->courseTable->fetchById($content->getCourseId());
        }

        $this->attemptTable->hasdownload($id);

        return new HtmlResponse($this->template->render('attempt::certificate', [
            'certificate' => $attempts,
            'score' => $score,
            'course' => $course,
            'topic' => $topic
        ]));
    }
}