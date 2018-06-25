<?php

namespace Exam\Action\Form;

use Exam\Form;
use Exam\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course\Model\ContentTable;
use User;

class Exam
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
     * @var ContentTable
     */
    private $contentTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\ExamTable $examTable, ContentTable $contentTable)
    {
        $this->template = $template;
        $this->examTable = $examTable;
		$this->contentTable = $contentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parentId = $request->getAttribute('parentId');
        $examId = $request->getAttribute('id');

        if ($examId) {
            /**
             * @var Model\Exam $exam
             */
            $exam = $this->examTable->fetchById($examId)->current();
        }

        $form = new Form\Exam();

        // bind object when id passed in url
        if (isset($exam) && $exam instanceof Model\Exam) {
            $form->bind($exam);
        }

        // populate courseId
        $content = $this->contentTable->fetchByContentId($parentId)->current();
        $form->setData(['courseId' => $content->getCourseId()]);

        // populate adminId
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();
        $form->setData(['adminId' => $currentUserId]);

        // populate parentId
        $form->setData(['parentId' => $parentId]);

        return new HtmlResponse($this->template->render('exam::form', [
            'form' => $form
        ]));
    }
}
