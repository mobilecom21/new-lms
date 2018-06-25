<?php

namespace Attempt\Action\View;

use Attempt\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use User\Model\UserTable;
use User\Model\UserMetaTable;
use Exam\Model\ExamTable;
use Course\Model\CourseTable;
use Topic\Model\TopicTable;
use Course\Model\ContentTable;

class ResultSet
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
     * @var Model\usermetaTable
     */
	private $userTable;

    /**
     * @var Model\usermetaTable
     */
	private $usermetaTable;

    /**
     * @var Model\examTable
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

    public function __construct(Template\TemplateRendererInterface $template, Model\AttemptTable $attemptTable, UserTable $userTable, UserMetaTable $usermetaTable, ExamTable $examTable, CourseTable $courseTable, TopicTable $topicTable, ContentTable $contentTable)
    {
        $this->template = $template;
        $this->attemptTable = $attemptTable;
		$this->userTable = $userTable;
		$this->usermetaTable = $usermetaTable;
		$this->examTable = $examTable;
		$this->courseTable = $courseTable;
		$this->topicTable = $topicTable;
		$this->contentTable = $contentTable;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return new HtmlResponse($this->template->render('attempt::resultset', [
            'resultSet' => $this->attemptTable->fetchAllOrderByIdDesc(),
			'exam' => $this->examTable,
			'topic' => $this->topicTable,
			'content' => $this->contentTable,
			'course' => $this->courseTable,
			'attempt' => $this->attemptTable,
			'user' => $this->userTable
        ]));
    }
}
