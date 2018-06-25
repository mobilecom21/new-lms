<?php

namespace File\Action\View;

use File\Model;
use User\Model\User;
use Tutor\Model\TutorStudentCourseTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Uploader\Uploader;

class Single
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
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var AttachmentTable
     */
    private $attachmentTable;

    /**
     * @var ContentTable;
     */
    private $contentTable;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Template\TemplateRendererInterface $template,
                                Model\FileTable $fileTable,
                                TutorStudentCourseTable $tutorStudentCourseTable,
                                AttachmentTable $attachmentTable,
                                ContentTable $contentTable,
                                Uploader $uploader)
    {
        $this->template = $template;
        $this->fileTable = $fileTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $attachment = $this->attachmentTable->fetchByAttachmentAndAttachmentId($id, 'File')->current();
        if (!$attachment) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }
        $topicId = $attachment->getTopicId();
        $content = $this->contentTable->fetchByContentId($topicId)->current();
        if (! $content) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }
        $courseId = $content->getCourseId();

        /**
         * @var User $currentUser
         */
        $currentUser = $request->getAttribute(User::class);
        $currentUserId = $currentUser->getId();
        $currentUserRole = $currentUser->getRole();

        if ('Rbac\Role\Administrator' == $currentUserRole) {
            $isAllowed = true;
        } elseif ('Rbac\Role\Tutor' == $currentUserRole) {
            $isAllowed = $this->tutorStudentCourseTable->isTutorAllowed($currentUserId, $courseId);
        } elseif ('Rbac\Role\Student' == $currentUserRole) {
            $isAllowed = $this->tutorStudentCourseTable->isStudentAllowed($currentUserId, $courseId);
        }

        if (! $isAllowed) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        $file = $this->fileTable->fetchById($id)->current();
        $uploads = $file->getUploads();
        $uploads = json_decode($uploads);
        $uploads = (Array)$uploads;
        $upload_id = reset($uploads);
        $upload = $this->uploader->get([$upload_id]);
        $upload = $upload[0] ?? [];

        if (empty($upload['path'])) {
            return new HtmlResponse($this->template->render('file::single', [
                'file' => $this->fileTable->fetchById($id)->current()
            ]));
        }

        $file = $this->uploader->download($upload['path']);
        if (empty($file->getContent())) {
            return new HtmlResponse($this->template->render('file::single', [
                'file' => $this->fileTable->fetchById($id)->current()
            ]));
        }

        $disposition = 'application/pdf' == $upload['type'] ? 'inline' : 'attachment';
        return new HtmlResponse(
            $file->getContent(),
            '200',
            [
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
                'Content-type' => $upload['type'],
                'Content-Length' => $upload['size'],
                'Content-Disposition' => $disposition . '; filename="' . $upload['name'].'"',
            ]
        );
    }
}
