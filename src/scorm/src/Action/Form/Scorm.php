<?php

namespace Scorm\Action\Form;

use Scorm\Form;
use Scorm\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course\Model\ContentTable;

class Scorm
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\ScormTable
     */
    private $scormTable;

    /**
     * @var ContentTable
     */
    private $contentTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\ScormTable $scormTable, ContentTable $contentTable)
    {
        $this->template = $template;
        $this->scormTable = $scormTable;
        $this->contentTable = $contentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parentId = $request->getAttribute('parentId');
        $scormId = $request->getAttribute('id');

        if ($scormId) {
            /**
             * @var Model\Scorm $scorm
             */
            $scorm = $this->scormTable->fetchById($scormId)->current();
        }

        $form = new Form\Scorm();

        // bind object when id passed in url
        if (isset($scorm) && $scorm instanceof Model\Scorm) {
            $form->bind($scorm);
        }

        // populate courseId
        $content = $this->contentTable->fetchByContentId($parentId)->current();
        $form->setData(['courseId' => $content->getCourseId()]);

        // populate parentId
        $form->setData(['parentId' => $parentId]);

        return new HtmlResponse($this->template->render('scorm::form', [
            'form' => $form
        ]));
    }
}
