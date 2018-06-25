<?php

namespace Scorm\Shared\Link\Form;

use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Scorm
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
            'href' => ($this->urlHelper)('scorm/form/scorm', ['parentId' => $parentId]),
            'label' => 'Scorm'
        ];
    }
}
