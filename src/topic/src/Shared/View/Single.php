<?php

namespace Topic\Shared\View;

use Topic\Model;
use Zend\Expressive\Template;

class Single
{

    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\TopicTable
     */
    private $topicTable;

    /**
     * @var Model\AttachmentTable
     */
    private $attachmentTable;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\TopicTable $topicTable,
        Model\AttachmentTable $attachmentTable
    )
    {

        $this->template = $template;
        $this->topicTable = $topicTable;
        $this->attachmentTable = $attachmentTable;
    }

    public function __invoke(
        int $parentId,
        int $id,
        bool $onlyRead = false,
        string $templateNamespace = null,
        string $routeNamespace = null
    ): string
    {

        return $this->template->render($this->prepareTemplateName($onlyRead, $templateNamespace), [
            'layout' => false,
            'parentId' => $parentId,
            'routeNamespace' => $routeNamespace,
            'topic' => $this->topicTable->fetchById($id)->current(),
            'attachments' => $this->attachmentTable->fetchByTopicId($id)
        ]);
    }

    protected function prepareTemplateName(?bool $onlyRead, ?string $templateNamespace): string
    {
        return ($templateNamespace ? $templateNamespace . '::topic/single/' : 'topic::single/') . ($onlyRead ? 'read' : 'write');
    }
}
