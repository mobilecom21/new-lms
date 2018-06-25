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
use Attempt\Model\AttemptTable;

class MultiCertificate
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

    public function __construct(Template\TemplateRendererInterface $template, AttemptTable $attemptTable, UserMetaTable $usermetaTable, ExamTable $examTable, CourseTable $courseTable, TopicTable $topicTable, ContentTable $contentTable)
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
		$data = $request->getParsedBody();
		$ids = $data['id'];

		foreach($ids as $dataId) {
			$inputId = explode("|",$dataId);
			$attemptId = $inputId[0];
			$paymentId = $inputId[1];

			$id = $attemptId;

			$attempts = $this->attemptTable->fetchById($id)->current();
			if($attempts) { 
				$score = $attempts->getScore(); 
				$date = date('d F Y', strtotime($attempts->getCreatedAt()));
			} else { 
				$score = 0; 
				$date = date('d F Y');
			}
			$usermeta = $this->usermetaTable->fetchByUserId($attempts->getStudentId());
			foreach ($usermeta as $meta) {
				$user_details[$meta->getName()] = $meta->getValue();
			}
			$studentname = $user_details['first_name'].' '.$user_details['last_name'];

			$exam = $this->examTable->fetchById($attempts->getExamId())->current();			
			if($exam) {
				$topic = $this->topicTable->fetchById($exam->getTopicId())->current();
				$content = $this->contentTable->fetchByContentId($topic->getId())->current();
				$course = $this->courseTable->fetchById($content->getCourseId());
				$coursename = $course->getName();
			}

			$mydata[$id]['studentname'] = $studentname;
			$mydata[$id]['coursename'] = $coursename;
			$mydata[$id]['score'] = $score;
			$mydata[$id]['date'] = $date;

		}

        return new HtmlResponse($this->template->render('certificate::multi-certificate', [
            'data' => $mydata,
        ]));
    }
}
