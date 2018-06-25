<?php

namespace Exam\Shared\View;

use Exam\Model;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Single
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Model\ExamTable
     */
    private $examTable;

    public function __construct(Template\TemplateRendererInterface $template, UrlHelper $urlHelper, Model\ExamTable $examTable)
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->examTable = $examTable;
    }

    public function __invoke(int $parentId, int $id, string $routeNamespace = null): array
    {
        /**
         * @var Model\Exam $exam
         */
        $exam = $this->examTable->fetchById($id)->current();

        return [
            'id' => $exam->getId(),
            'type' => 'exam',
            'name' => $exam->getName(),
            'href' => $this->urlHelper->generate($this->prepareSingleRouteName($routeNamespace), ['id' => $exam->getId()]),
            'form' => [
                'href' => $this->urlHelper->generate('exam/form/exam', ['parentId' => $parentId, 'id' =>
                    $exam->getId()])
            ]
        ];
    }

    protected function prepareSingleRouteName(?string $routeNamespace): string
    {
        return ($routeNamespace ? $routeNamespace . '/' : '') . 'exam/view/single';
    }
}
