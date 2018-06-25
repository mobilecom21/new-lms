<?php

namespace Scorm\Action\View;

use Scorm\Model;
use Tutor\Model\TutorStudentCourseTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use User\Model\User;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Uploader\Uploader;
use ZipArchive;
use Zend\Diactoros\Response\RedirectResponse;
use DOMDocument;

class Single
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
                                Model\ScormTable $scormTable,
                                TutorStudentCourseTable $tutorStudentCourseTable,
                                AttachmentTable $attachmentTable,
                                ContentTable $contentTable,
                                Uploader $uploader)
    {
        $this->template = $template;
        $this->scormTable = $scormTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $attachment = $this->attachmentTable->fetchByAttachmentAndAttachmentId($id, 'Scorm')->current();
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

        $scorm = $this->scormTable->fetchById($id)->current();
        $uploads = $scorm->getUploads();
        $uploads = json_decode($uploads);
        $uploads = (Array)$uploads;
        $upload_id = reset($uploads);
        $upload = $this->uploader->get([$upload_id]);
        $upload = $upload[0] ?? [];
        if (empty($upload['path'])) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }
        $fileName = substr($upload['path'], 0,-4);

        $unzipped = 'data/media/scorm' . $fileName;
        $zipped = 'data/media/' . $upload['path'];
        if (file_exists($zipped) && ! file_exists($unzipped)) {
            $zip = new ZipArchive;
            if ($zip->open($zipped) === TRUE) {
                $zip->extractTo($unzipped);
                $zip->close();
            }
        }
        $imsmanifest = $unzipped . '/imsmanifest.xml';
        if (! file_exists($unzipped) || ! file_exists($imsmanifest)) {
            return new HtmlResponse($this->template->render('scorm::single', [
                'scorm' => $scorm
            ]));
        }
        $href = $type = '';
        $files = array();
        $dom = new DomDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->load($imsmanifest);
        $resources = $dom->getElementsByTagName('resources');
        foreach ($resources as $resources_row) {
            $resource = $resources_row->getElementsByTagName('resource');
            foreach ($resource as $resource_row) {
                $href = $resource_row->getAttribute('href');
                $type = $resource_row->getAttribute('type');
                $resource_files = $resource_row->getElementsByTagName('file');
                foreach ($resource_files as $resource_files_row) {
                    $files[] = $resource_files_row->getAttribute('href');
                }
            }
        }
        $landing = $unzipped . '/' . $href;
        if ('webcontent' != $type || empty($href) || ! file_exists($landing)) {
            return new HtmlResponse($this->template->render('scorm::single', [
                'scorm' => $scorm
            ]));
        }
        $landing = str_replace('data/media/scorm', 'player', $landing);
        return new HtmlResponse($this->template->render('scorm::single', [
            'scorm' => $scorm,
            'landing' => $landing
        ]));
    }
}
