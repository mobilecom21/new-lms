<?php

namespace Exam\Shared\Form;

use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;

class Link
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
            'href' => ($this->urlHelper)('exam/form/exam', ['parentId' => $parentId]),
            'label' => 'Exam'
        ];
    }
}
