<?php

namespace Assignment\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Uploader\Uploader;
use User\Model\User;
use Tutor\Model\TutorStudentCourseTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use Aws\S3\S3Client;

class Resource
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

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

    /**
     * @var S3Client
     */
    private $s3Client;

    public function __construct(Template\TemplateRendererInterface $template,
                                TutorStudentCourseTable $tutorStudentCourseTable,
                                AttachmentTable $attachmentTable,
                                ContentTable $contentTable,
                                Uploader $uploader,
                                S3Client $s3Client)
    {
        $this->template = $template;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->uploader = $uploader;
        $this->s3Client = $s3Client;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $fileId = $request->getAttribute('fileId');
        $attachment = $this->attachmentTable->fetchByAttachmentAndAttachmentId($id, 'Assignment')->current();
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

        $upload = $this->uploader->get([$fileId]);
        $upload = $upload[0] ?? [];

        if (empty($upload['path'])) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        $file = $this->uploader->download($upload['path']);

        if (empty($file->getContent())) {
            return new HtmlResponse($this->template->render('error::404'), 404);
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