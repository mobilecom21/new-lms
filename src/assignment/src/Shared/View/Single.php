<?php

namespace Assignment\Shared\View;

use Assignment\Model;
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
     * @var Model\AssignmentTable
     */
    private $assignmentTable;

    public function __construct(Template\TemplateRendererInterface $template, UrlHelper $urlHelper, Model\AssignmentTable $assignmentTable)
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->assignmentTable = $assignmentTable;
    }

    public function __invoke(int $parentId, int $id, string $routeNamespace = null): array
    {
        /**
         * @var Model\Assignment $assignment
         */
        $assignment = $this->assignmentTable->fetchById($id);
        return [
            'id' => $assignment->getId(),
            'type' => 'assignment',
            'name' => $assignment->getName(),
            'summary' => $assignment->getSummary(),
            'grade' => $assignment->getGrade(),
            'href' => $this->urlHelper->generate($this->prepareSingleRouteName($routeNamespace), ['id' => $assignment->getId()]),
            'form' => [
                'href' => $this->urlHelper->generate('assignment/form/assignment', ['parentId' => $parentId, 'id' =>
                    $assignment->getId()])
            ]
        ];
    }

    protected function prepareSingleRouteName(?string $routeNamespace): string
    {
        return ($routeNamespace ? $routeNamespace . '/' : '') . 'assignment/view/single';
    }
}
