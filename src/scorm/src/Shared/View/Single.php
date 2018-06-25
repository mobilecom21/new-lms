<?php

namespace Scorm\Shared\View;

use Scorm\Model;
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
     * @var Model\ScormTable
     */
    private $scormTable;

    public function __construct(Template\TemplateRendererInterface $template, UrlHelper $urlHelper, Model\ScormTable $scormTable)
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->scormTable = $scormTable;
    }

    public function __invoke(int $parentId, int $id, string $routeNamespace = null): array
    {
        /**
         * @var Model\Scorm $scorm
         */
        $scorm = $this->scormTable->fetchById($id)->current();

        return [
            'id' => $scorm->getId(),
            'type' => 'scorm',
            'name' => $scorm->getName(),
            'summary' => $scorm->getSummary(),
            'href' => $this->urlHelper->generate($this->prepareSingleRouteName($routeNamespace), ['id' => $scorm->getId()]),
            'form' => [
                'href' => $this->urlHelper->generate('scorm/form/scorm', ['parentId' => $parentId, 'id' =>
                    $scorm->getId()])
            ]
        ];
    }

    protected function prepareSingleRouteName(?string $routeNamespace): string
    {
        return ($routeNamespace ? $routeNamespace . '/' : '') . 'scorm/view/single';
    }
}
