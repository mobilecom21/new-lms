<?php

namespace Topic\Action\Form;

use Topic\Form;
use Topic\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Topic
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;
    /**
     * @var Model\TopicTable
     */
    private $topicTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\TopicTable $topicTable)
    {
        $this->template = $template;
        $this->topicTable = $topicTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parentId = $request->getAttribute('parentId');
        $topicId = $request->getAttribute('id');

        if ($topicId) {
            /**
             * @var Model\Topic $topic
             */
            $topic = $this->topicTable->fetchById($topicId)->current();
            
        }

        $form = new Form\Topic();

        // bind object when id passed in url
        if (isset($topic) && $topic instanceof Model\Topic) {
            $form->bind($topic);
        }

        // populate parentId
        $form->setData(['parentId' => $parentId]);

        return new HtmlResponse($this->template->render('topic::form', [
            'form' => $form
        ]));
    }
}
