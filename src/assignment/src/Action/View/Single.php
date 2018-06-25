<?php

namespace Assignment\Action\View;

use Assignment\Form;
use Assignment\Model;
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
     * @var Model\AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var Model\AssignmentTable
     */
    private $assignmentTable;

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

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\AssignmentWorkTable $assignmentWorkTable,
        Model\AssignmentTable $assignmentTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        AttachmentTable $attachmentTable,
        ContentTable $contentTable,
        Uploader $uploader
    ) {
        $this->template = $template;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->assignmentTable = $assignmentTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
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

        $assignmentFiles = [];
        $assignment = $this->assignmentTable->fetchById($id);
        if ($assignment) {
            $uploads = $assignment->getUploads();
            $uploads = json_decode($uploads);
            $uploads = (Array)$uploads;
            if (count($uploads) > 0) {
                $assignmentFiles = $this->uploader->get($uploads);
            }
            $assignmentWork = $this->assignmentWorkTable->byWorkerAndAssignment($currentUserId, $id);

            return new HtmlResponse($this->template->render('assignment::single', [
                'lastAssignmentWork' => $assignmentWork[0] ?? null,
                'assignmentWorkResultSet' => $assignmentWork,
                'assignment' => $this->assignmentTable->fetchById($id),
                'workForm' => new Form\Work\Work,
                'uploader' => $this->uploader,
                'assignmentFiles' => $assignmentFiles
            ]));
        }
        return new HtmlResponse($this->template->render('error::404'), 404);
    }
}
