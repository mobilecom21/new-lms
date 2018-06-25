<?php

namespace Options\Action\Form;

use Options\Form;
use Options\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Options
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;
    /**
     * @var Model\OptionsTable
     */
    private $optionsTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\OptionsTable $optionsTable)
    {
        $this->template = $template;
        $this->optionsTable = $optionsTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $form = new Form\Options();
        $options = $this->optionsTable->fetchAll()->toArray();
        return new HtmlResponse($this->template->render('options::form', [
            'form' => $form,
            'options' => $options
        ]));
    }
}
