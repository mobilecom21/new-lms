<?php

namespace Certificate\Action\View;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use User\Model\UserMetaTable;
use Exam\Model\ExamTable;
use Course\Model\CourseTable;
use Topic\Model\TopicTable;
use Course\Model\ContentTable;
use Attempt;

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

    public function __construct(Template\TemplateRendererInterface $template, Attempt\Model\AttemptTable $attemptTable, UserMetaTable $usermetaTable, ExamTable $examTable, CourseTable $courseTable, TopicTable $topicTable, ContentTable $contentTable)
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
		$id = $request->getAttribute('id');
		$attempts = $this->attemptTable->fetchById($id)->current();
		if($attempts) { 
			$score = $attempts->getScore(); 
			$date = date('d F Y', strtotime($attempts->getCreatedAt()));
		} else { 
			$score = 0; 
			$date = date('d F Y');
		}

		$exam = $this->examTable->fetchById($attempts->getExamId())->current();
		if($exam) {
			$topic = $this->topicTable->fetchById($exam->getTopicId())->current();
			$content = $this->contentTable->fetchByContentId($topic->getId())->current();
			$course = $this->courseTable->fetchById($content->getCourseId());
		}
		$user = $this->usermetaTable->fetchByUserId($attempts->getStudentId());

		$download = $this->attemptTable->hasdownload($id);

        return new HtmlResponse($this->template->render('certificate::single-certificate', [
            'certificate' => $attempts,
			'score' => $score,
			'course' => $course,
			'topic' => $topic,
			'user' => $user,
			'date' => $date,
        ]));
    }
}
