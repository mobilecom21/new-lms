<?php

namespace Message\Action\Form;

use Course\Model\ContentTable;
use Message;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Quiz
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Message\Form\Quiz
     */
    private $quizForm;

    /**
     * @var ContentTable
     */
    private $contentTable;

    public function __construct(Message\Form\Quiz $quizForm, ContentTable $contentTable, Template\TemplateRendererInterface $template)
    {
        $this->template = $template;
        $this->quizForm = $quizForm;
        $this->contentTable = $contentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $courseId = $request->getAttribute('courseId');

        $valueOptions = [
            'All modules' => 'All modules'
        ];
        foreach ($this->contentTable->contentByCourseId($courseId) as $content) {
            $valueOptions[] = [
                'value' => $content->getName(),
                'label' => $content->getName()
            ];
        }
        $this->quizForm->get('content')
            ->setValueOptions($valueOptions);

        return new HtmlResponse($this->template->render('message::form/quiz', [
            'form' => $this->quizForm,
            'courseId' => $courseId
        ]));
    }
}