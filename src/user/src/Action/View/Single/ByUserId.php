<?php

namespace User\Action\View\Single;

use User\Model;
use Assignment\Model\AssignmentWorkTable;
use Tutor\Model\TutorStudentCourseTable;
use Course\Model\ContentTable;
use Course\Model\CourseTable;
use Topic\Model\AttachmentTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class ByUserId
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\userMetaTable
     */
    private $userMetaTable;

    /**
     * @var AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var ContentTable;
     */
    private $contentTable;

    /**
     * @var CourseTable;
     */
    private $courseTable;

    /**
     * @var AttachmentTable
     */
    private $attachmentTable;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $currentUserRole;

    /**
     * @var int
     */
    private $currentUserId;

    /**
     * @var string
     */
    private $singleRouteName;

    /**
     * @var string
     */
    private $templateNamespace;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\UserTable $userTable,
        Model\UserMetaTable $userMetaTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        AssignmentWorkTable $assignmentWorkTable,
        ContentTable $contentTable,
        CourseTable $courseTable,
        AttachmentTable $attachmentTable,
        int $userId,
        string $role,
        string $singleRouteName,
        string $templateName
    ) {

        $this->template = $template;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->contentTable = $contentTable;
        $this->courseTable = $courseTable;
        $this->attachmentTable = $attachmentTable;
        $this->currentUserRole = $role;
        $this->currentUserId = $userId;
        $this->singleRouteName = $singleRouteName;
        $this->templateName = $templateName;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var string
         */
        $id = $request->getAttribute('id');
        if ('Rbac\Role\Tutor' == $this->currentUserRole && ! $this->tutorStudentCourseTable->isTutorForStudent($this->currentUserId, $id)) {
            return new HtmlResponse($this->template->render('error::404'), 404);
        }

        $assignmentWorkResultSet = $this->assignmentWorkTable->byWorker($id);

        $courses = [];
        if ('Rbac\Role\Tutor' == $this->currentUserRole) {
            $tutorId = $this->currentUserId;
            $studentId = $id;
            $courses = $this->tutorStudentCourseTable->fetchByStudentAndTutor($studentId, $tutorId)->toArray();
            foreach ($courses as $key => $course) {
                $modules = $this->contentTable->contentByCourseId($course['course_id'])->toArray();
                foreach ($modules as $key1 => $module) {
                    if (empty($module['required'])) {
                        unset($modules[$key1]);
                        continue;
                    }
                    $assignments = $this->attachmentTable->fetchByAttachmentAndTopicId('Assignment', $module['id'])->toArray();
                    foreach($assignments as $key2 => $assignment) {
                        if($this->assignmentWorkTable->hasCompleteByWorkerAndTutorAndAssignment($studentId, $tutorId, $assignment['attachment_id'])) {
                            $modules[$key1]['status'] = 'btn-success';
                            break;
                        } elseif($this->assignmentWorkTable->hasFailedByWorkerAndTutorAndAssignment($studentId, $tutorId, $assignment['attachment_id'])) {
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
        } elseif ('Rbac\Role\Administrator' == $this->currentUserRole) {
            $studentId = $id;
            $courses = $this->tutorStudentCourseTable->fetchByStudentId($studentId)->toArray();
            foreach ($courses as $key => $course) {
                $modules = $this->contentTable->contentByCourseId($course['course_id'])->toArray();
                foreach ($modules as $key1 => $module) {
                    if (empty($module['required'])) {
                        unset($modules[$key1]);
                        continue;
                    }
                    $assignments = $this->attachmentTable->fetchByAttachmentAndTopicId('Assignment', $module['id'])->toArray();
                    foreach($assignments as $key2 => $assignment) {
                        if($this->assignmentWorkTable->hasCompleteByWorkerAndAssignment($studentId, $assignment['attachment_id'])) {
                            $modules[$key1]['status'] = 'btn-success';
                            break;
                        } elseif($this->assignmentWorkTable->hasFailedByWorkerAndAssignment($studentId, $assignment['attachment_id'])) {
                            $modules[$key1]['status'] = 'btn-danger';
                            break;
                        }
                    }
                    if (empty($modules[$key1]['status'])) {
                        $modules[$key1]['status'] = 'btn-secondary';
                    }
                }
                $courseInfo =  $this->courseTable->fetchById($course['course_id']);
                if ($course['end_of_support'] > 0) {
                    $endTime = new \DateTime();
                    $endTime->setTimestamp($course['end_of_support']);
                    $courses[$key]['end_of_support'] = $endTime->format('d/m/Y');
                } else {
                    $course['end_of_support'] = '';
                }
                $courses[$key]['name'] = $courseInfo->getName();
                $courses[$key]['modules'] = $modules;
            }
        }

        /**
         * @var Model\Course $course
         */
        $user = $this->userTable->oneById($id);

        if (false === $user) {
            return $delegate->process($request);
        }
        return new HtmlResponse($this->template->render($this->templateName, [
            'user' => $user,
            'usermeta' => $this->userMetaTable->fetchByUserId($id),
            'role' => $this->currentUserRole,
            'assignmentWorkResultSet' => $assignmentWorkResultSet,
            'courses' => $courses
        ]));
    }
}
