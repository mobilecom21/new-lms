<?php

namespace File\Action\Form;

use File\Form;
use File\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course\Model\ContentTable;

class File
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\FileTable
     */
    private $fileTable;

    /**
     * @var ContentTable
     */
    private $contentTable;


    public function __construct(Template\TemplateRendererInterface $template, Model\FileTable $fileTable, ContentTable $contentTable)
    {
        $this->template = $template;
        $this->fileTable = $fileTable;
        $this->contentTable = $contentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parentId = $request->getAttribute('parentId');
        $fileId = $request->getAttribute('id');

        if ($fileId) {
            /**
             * @var Model\File $file
             */
            $file = $this->fileTable->fetchById($fileId)->current();
        }

        $form = new Form\File();

        // bind object when id passed in url
        if (isset($file) && $file instanceof Model\File) {
            $form->bind($file);
        }

        // populate courseId
        $content = $this->contentTable->fetchByContentId($parentId)->current();
        $form->setData(['courseId' => $content->getCourseId()]);

        // populate parentId
        $form->setData(['parentId' => $parentId]);

        return new HtmlResponse($this->template->render('file::form', [
            'form' => $form
        ]));
    }
}
