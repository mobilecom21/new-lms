<?php

namespace Assignment\Action\Post\Work;

use Assignment\InputFilter;
use Assignment\Model\AssignmentWorkTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uploader\Uploader;
use User;
use Tutor;
use Topic;
use Options;
use Course;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;

class Work
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var User\Model\UserActivityTable
     */
    private $userActivityTable;

    /**
     * @var Tutor\Model\TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var Topic\Model\TopicTable
     */
    private $topicTable;

    /**
     * @var Options
     */
    private $optionsTable;

    /**
     * @var Topic\Model\AttachmentTable
     */
    private $topicAttachmentTable;

    /**
     * @var Course\Model\ContentTable
     */
    private $contentTable;

    /**
     * @var Course\Model\CourseTable
     */
    private $courseTable;

    /**
     * @var Mail\Transport\TransportInterface
     */
    private $transportMail;

    /**
     * @var Size
     */
    private $sizeValidator;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        Template\TemplateRendererInterface $template,
        AssignmentWorkTable $assignmentWorkTable,
        Uploader $uploader,
        User\Model\UserTable $userTable,
        User\Model\UserMetaTable $userMetaTable,
        User\Model\UserActivityTable $userActivityTable,
        Tutor\Model\TutorStudentCourseTable $tutorStudentCourseTable,
        Topic\Model\TopicTable $topicTable,
        Options\Model\OptionsTable $optionsTable,
        Topic\Model\AttachmentTable $topicAttachmentTable,
        Course\Model\ContentTable $contentTable,
        Course\Model\CourseTable $courseTable,
        Mail\Transport\TransportInterface $transportMail,
        UrlHelper $urlHelper
    ) {
        $this->template = $template;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->uploader = $uploader;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->userActivityTable = $userActivityTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->topicTable = $topicTable;
        $this->optionsTable = $optionsTable;
        $this->topicAttachmentTable = $topicAttachmentTable;
        $this->contentTable = $contentTable;
        $this->courseTable = $courseTable;
        $this->transportMail = $transportMail;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $response = new JsonResponse([
            'redirectTo' => ($this->urlHelper)('student/assignment/view/single', ['id' => $id])
        ]);;
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Work\Work();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();

        $assignmentUploader = ! empty($data['assignment']) ? (int)$data['assignment'] : 0;
        if (0 == $assignmentUploader) {
            return new JsonResponse([
                'errors' => [
                    'assignment' => ['message' => 'Please upload assignment file']
                ]
            ]);
        }

        $uploadedBy = $this->uploader->getUploadedBy($assignmentUploader);
        if ($uploadedBy != $currentUserId) {
            return new JsonResponse([
                'errors' => [
                    'assignment' => ['message' => 'Please upload assignment file']
                ]
            ]);
        }

        $this->assignmentWorkTable->submitWork($currentUserId, $id, $assignmentUploader);

        $topicAttachment = $this->topicAttachmentTable->fetchByAttachmentAndAttachmentId($id, 'Assignment')->current();
        if (!$topicAttachment) {
            return $response;
        }
        $courseContent = $this->contentTable->fetchByContentAndContentId($topicAttachment->getTopicId(), 'Topic')->current();
        if (!$courseContent) {
            return $response;
        }
        $courseId = (int) $courseContent->getCourseId();

        $course = $this->courseTable->fetchById($courseId);
        if (! $course) {
            return $response;
        }

        $tutorStudentCourse = $this->tutorStudentCourseTable->fetchTutorForStudentAndCourse($currentUser->getId(), $courseId);
        if (!$tutorStudentCourse) {
            return $response;
        }
        $tutor = (int) $tutorStudentCourse->getTutorId();

        $message['sender'] = $currentUserId;
        $message['receiver'] = $tutor;
        $message['text'] = json_encode(['Message' => 'Student added new work to course: ' . $course->getName()]);

        $topic = $this->topicTable->fetchById($topicAttachment->getTopicId())->current();
        $first_name = $this->userMetaTable->getMetaByName($currentUserId, 'first_name')->current();
        $last_name = $this->userMetaTable->getMetaByName($currentUserId, 'last_name')->current();
        $topic_link = '<a href="/tutor/assignment/view/work/resultset/due">' . $topic->getName() . '</a>';
        $this->userActivityTable->add($tutor, json_encode(['text' => $first_name->getValue() . ' ' . $last_name->getValue() . ' added new work to <strong>' . $topic_link . '</strong>']));

        /**
         * @var User\Model\User $receiver
         */
        $receiver = $this->userTable->oneById($message['receiver']);

        $tutor_first_name = $this->userMetaTable->getMetaByName($tutor, 'first_name')->current();
        $tutor_last_name = $this->userMetaTable->getMetaByName($tutor, 'last_name')->current();
        $htmlMarkup = $this->template->render('emails::work', [
            'layout' => false,
            'tutorName' => $tutor_first_name->getValue() . ' ' . $tutor_last_name->getValue(),
            'studentName' => $first_name->getValue() . ' ' . $last_name->getValue(),
            'module' => $topic->getName(),
            'courseName' => $course->getName(),
        ]);
        $html = new MimePart($htmlMarkup);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        if ($this->optionsTable->optionExists('from_email')) {
            $from = $this->optionsTable->fetchByName('from_email')['value'];
            $message = new Mail\Message();
            $message->addTo($receiver->getIdentity())
                ->addFrom($from)
                ->setSubject($first_name->getValue() . ' ' . $last_name->getValue() . ' has submitted work for review')
                ->setBody($body);
            $this->transportMail->send($message);
        }

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('student/assignment/view/single', ['id' => $id])
        ]);
    }
}