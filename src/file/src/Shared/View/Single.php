<?php

namespace File\Shared\View;

use File\Model;
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
     * @var Model\FileTable
     */
    private $fileTable;

    public function __construct(Template\TemplateRendererInterface $template, UrlHelper $urlHelper, Model\FileTable $fileTable)
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->fileTable = $fileTable;
    }

    public function __invoke(int $parentId, int $id, string $routeNamespace = null): array
    {
        /**
         * @var Model\File $file
         */
        $file = $this->fileTable->fetchById($id)->current();

        return [
            'id' => $file->getId(),
            'type' => 'file',
            'name' => $file->getName(),
            'href' => $this->urlHelper->generate($this->prepareSingleRouteName($routeNamespace), ['id' => $file->getId()]),
            'form' => [
                'href' => $this->urlHelper->generate('file/form/file', ['parentId' => $parentId, 'id' =>
                    $file->getId()])
            ]
        ];
    }

    protected function prepareSingleRouteName(?string $routeNamespace): string
    {
        return ($routeNamespace ? $routeNamespace . '/' : '') . 'file/view/single';
    }
}
