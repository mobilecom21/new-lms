<?php

namespace Assignment\Action\View\Work;

use Assignment\Form;
use Assignment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Model\User;
use Tutor\Model\TutorStudentCourseTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use Course\Model\CourseTable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

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
     * @var CourseTable;
     */
    private $courseTable;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\AssignmentWorkTable $assignmentWorkTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        AttachmentTable $attachmentTable,
        ContentTable $contentTable,
        CourseTable $courseTable
    ) {
        $this->template = $template;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->courseTable = $courseTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User $user
         */
        $user = $request->getAttribute(User::class);
        $id = $request->getAttribute('id');
        $feedbackForm = new Form\Work\Feedback();
        $assignmentWork = $this->assignmentWorkTable->byWorkAndTutor($id, $user->getId());
        $this->assignmentWorkTable->viewed($id, $user->getId());
        if (empty($assignmentWork[0])) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        $assignmentID = $assignmentWork[0]->assignment()->getId();
        $worker = $assignmentWork[0]->worker()->getId();
        $tutor = $assignmentWork[0]->tutor()->getId();

        $courses = $this->tutorStudentCourseTable->fetchByStudentAndTutor($worker, $tutor)->toArray();

        foreach ($courses as $key => $course) {
            $modules = $this->contentTable->contentByCourseId($course['course_id'])->toArray();
            foreach ($modules as $key1 => $module) {
                if (empty($module['required'])) {
                    unset($modules[$key1]);
                    continue;
                }
                $assignments = $this->attachmentTable->fetchByAttachmentAndTopicId('Assignment', $module['id'])->toArray();
                foreach($assignments as $key2 => $assignment) {
                    if($this->assignmentWorkTable->hasCompleteByWorkerAndTutorAndAssignment($worker, $tutor, $assignment['attachment_id'])) {
                        $modules[$key1]['status'] = 'btn-success';
                        break;
                    } elseif($this->assignmentWorkTable->hasFailedByWorkerAndTutorAndAssignment($worker, $tutor, $assignment['attachment_id'])) {
                        $modules[$key1]['status'] = 'btn-danger';
                        break;
                    }
                }
                if (empty($modules[$key1]['status'])) {
                    $modules[$key1]['status'] = 'btn-secondary';
                }
            }
            $course =  $this->courseTable->fetchById($course['course_id']);
            $courses[$key]['name'] = $course->getName();
            $courses[$key]['modules'] = $modules;
        }

        $lastWorkId = $this->assignmentWorkTable->fetchLastWorkByWorkerAndAssignment($worker, $assignmentID);
        return new HtmlResponse($this->template->render('assignment::work/single', [
            'lastWork' => $lastWorkId > 0 && $lastWorkId == $id ?? false,
            'assignmentWork' => $assignmentWork[0],
            'feedbackForm' => $feedbackForm,
            'courses' => $courses
        ]));
    }
}
