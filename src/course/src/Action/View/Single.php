<?php

namespace Course\Action\View;

use Course\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Single
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CourseTable
     */
    private $courseTable;

    /**
     * @var Model\ContentTable
     */
    private $contentTable;

    /**
     * @var string
     */
    private $templateName;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\CourseTable $courseTable,
        Model\ContentTable $contentTable,
        string $templateName = 'course::single/write'
    ) {
        $this->template = $template;
        $this->courseTable = $courseTable;
        $this->contentTable = $contentTable;
        $this->templateName = $templateName;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var string
         */
        $id = $request->getAttribute('id');

        /**
         * @var Model\Course $course
         */
        $course = $this->courseTable->fetchById($id);

        if (false === $course) {
            return $delegate->process($request);
        }

        return new HtmlResponse($this->template->render($this->templateName, [
            'course' => $course,
            'content' => $this->contentTable->fetchByCourseId($id)
        ]));
    }
}
