<?php

namespace Assignment\Action\Form;

use Assignment\Form;
use Assignment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course\Model\ContentTable;

class Assignment
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\AssignmentTable
     */
    private $assignmentTable;

    /**
     * @var ContentTable
     */
    private $contentTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\AssignmentTable $assignmentTable, ContentTable $contentTable)
    {
        $this->template = $template;
        $this->assignmentTable = $assignmentTable;
        $this->contentTable = $contentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parentId = $request->getAttribute('parentId');
        $assignmentId = $request->getAttribute('id');

        if ($assignmentId) {
            /**
             * @var Model\Assignment $assignment
             */
            $assignment = $this->assignmentTable->fetchById($assignmentId);
        }

        $form = new Form\Assignment();

        // bind object when id passed in url
        if (isset($assignment) && $assignment instanceof Model\Assignment) {
            $form->bind($assignment);
        }

        // populate courseId
        $content = $this->contentTable->fetchByContentId($parentId)->current();
        $form->setData(['courseId' => $content->getCourseId()]);

        // populate parentId
        $form->setData(['parentId' => $parentId]);

        return new HtmlResponse($this->template->render('assignment::form', [
            'form' => $form
        ]));
    }
}
