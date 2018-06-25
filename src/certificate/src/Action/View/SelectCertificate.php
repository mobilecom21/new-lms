<?php

namespace Certificate\Action\View;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Exam\Model\ExamTable;
use User\Model\UserMetaTable;
use Course\Model\CourseUserTable;
use Topic\Model\TopicTable;
use Course\Model\ContentTable;
use User;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class SelectCertificate
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\ExamTable
     */
	private $examTable;

    /**
     * @var Model\UserMetaTable
     */
	private $usermetaTable;

    /**
     * @var Model\courseTable
     */
	private $courseUserTable;

    /**
     * @var Model\topicTable
     */
	private $topicTable;

    /**
     * @var Model\contentTable
     */
	private $contentTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Template\TemplateRendererInterface $template, ExamTable $examTable, UserMetaTable $usermetaTable, CourseUserTable $courseUserTable, TopicTable $topicTable, ContentTable $contentTable, UrlHelper $urlHelper)
    {
        $this->template = $template;
		$this->examTable = $examTable;
		$this->usermetaTable = $usermetaTable;
		$this->courseUserTable = $courseUserTable;
		$this->topicTable = $topicTable;
		$this->contentTable = $contentTable;
        $this->urlHelper = $urlHelper;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
		$coupon = $request->getAttribute('coupon');
		$currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();
		$courseuser = $this->courseUserTable->fetchByUserId($currentUserId);

		$examIds = array();

		if(count($courseuser) > 0) {
			$n = 0;			
			foreach($courseuser as $course) {
				$courseId = $course->getId();
				$contents = $this->contentTable->fetchByCourseId($courseId);
				$examIdsReserve = array();
				$k = 0;
				if(count($contents) > 0) {
					foreach ($contents as $content) {
						$topicId = $content->getContentId();
						$exams = $this->examTable->fetchByTopicId($topicId);
						if(count($exams) > 0) {
							foreach ($exams as $exam) {
								$examIds[$n] = $exam->getId();
								$examIdsReserve[$k] = $exam->getId();
								$n++;
								$k++;
							}
						}
					}
				}
				$examIdsbyCourseId[$courseId] = $examIdsReserve;
			}
		}

		if(count($examIds) == 1) {
			return new RedirectResponse(($this->urlHelper)('certificate/form/payment',['id' => $examIds[0],'coupon' => $coupon]));			
		}

		$courseuser = $this->courseUserTable->fetchByUserId($currentUserId);

        return new HtmlResponse($this->template->render('certificate::select-certificate', [
            'examIds' => $examIds,
			'courseUser' => $courseuser,
			'examTable' => $this->examTable,
			'examIdsbyCourseId' => $examIdsbyCourseId,
			'coupon' => $coupon,
        ]));
    }
}
