<?php

namespace Assignment\Shared\Link\Form;

use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Assignment
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Template\TemplateRendererInterface $template, UrlHelper $urlHelper)
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(int $parentId): array
    {
        return [
            'href' => ($this->urlHelper)('assignment/form/assignment', ['parentId' => $parentId]),
            'label' => 'Assignment'
        ];
    }
}
