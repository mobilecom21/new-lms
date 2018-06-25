<?php

namespace Assignment\Action\Post\Work;

use Assignment\InputFilter;
use Assignment\Model\AssignmentWorkTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uploader\Uploader;
use User;
use Course;
use Topic;
use Options;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;

class Feedback
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
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var Course\Model\ContentTable
     */
    private $contentTable;

    /**
     * @var Course\Model\CourseTable
     */
    private $courseTable;

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
     * @var User\Model\UserActivityTable
     */
    private $userActivityTable;

    /**
     * @var Mail\Transport\TransportInterface
     */
    private $transportMail;

    public function __construct(
        Template\TemplateRendererInterface $template,
        AssignmentWorkTable $assignmentWorkTable,
        Uploader $uploader,
        UrlHelper $urlHelper,
        User\Model\UserTable $userTable,
        User\Model\UserMetaTable $userMetaTable,
        Course\Model\ContentTable $contentTable,
        Course\Model\CourseTable $courseTable,
        Topic\Model\TopicTable $topicTable,
        Options\Model\OptionsTable $optionsTable,
        Topic\Model\AttachmentTable $topicAttachmentTable,
        User\Model\UserActivityTable $userActivityTable,
        Mail\Transport\TransportInterface $transportMail
    )
    {
        $this->template = $template;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->uploader = $uploader;
        $this->urlHelper = $urlHelper;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->contentTable = $contentTable;
        $this->courseTable = $courseTable;
        $this->topicTable = $topicTable;
        $this->optionsTable = $optionsTable;
        $this->topicAttachmentTable = $topicAttachmentTable;
        $this->userActivityTable = $userActivityTable;
        $this->transportMail = $transportMail;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Work\Feedback();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $work = $this->assignmentWorkTable->fetchById($id)->toArray();
        if (! empty($work[0]['updated_at'])) {
            $diff = time() - $work[0]['updated_at'];
        } else {
            $diff = time() - $work[0]['created_at'];
        }

        /**
         * @var User\Model\User $user
         */
        $user = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $user->getId();

        $feedbackUploader = ! empty($data['feedback']) ? (int)$data['feedback'] : 0;

        if (0 == $feedbackUploader) {
            return new JsonResponse([
                'errors' => [
                    'feedback' => ['message' => 'Please upload feedback file']
                ]
            ]);
        }

        $uploadedBy = $this->uploader->getUploadedBy($feedbackUploader);
        if ($uploadedBy != $currentUserId) {
            return new JsonResponse([
                'errors' => [
                    'feedback' => ['message' => 'Please upload feedback file']
                ]
            ]);
        }

        $this->assignmentWorkTable->markWork($currentUserId, $id, $feedbackUploader, $filter->getValue('failed'));

        $userMetas = $this->userMetaTable->fetchByUserId($currentUserId)->toArray();
        $marking_time = $marking_count = 0;
        foreach ($userMetas as $userMeta) {
            if ('marking_time' == $userMeta['name']) {
                $marking_time_exists = 1;
                $marking_time = $userMeta['value'];
            } elseif ('marking_count' == $userMeta['name']) {
                $marking_count_exists = 1;
                $marking_count = $userMeta['value'];
            }
        }

        $marking_time +=  round($diff / 86400);
        $marking_count++;

        if (! empty($marking_time_exists)) {
            $this->userMetaTable->update($currentUserId, 'marking_time', $marking_time);
        } else {
            $this->userMetaTable->add($currentUserId, 'marking_time', $marking_time);
        }
        if (! empty($marking_count_exists)) {
            $this->userMetaTable->update($currentUserId, 'marking_count', $marking_count);
        } else {
            $this->userMetaTable->add($currentUserId, 'marking_count', $marking_count);
        }

        $tutor = $work[0]['tutor'];
        $worker = $work[0]['worker'];
        $assignment = $work[0]['assignment'];
        $topicAttachment = $this->topicAttachmentTable->fetchByAttachmentAndAttachmentId($assignment, 'Assignment')->current();
        if ($topicAttachment && $tutor > 0 && $worker > 0 && $assignment > 0) {
            $topic = $this->topicTable->fetchById($topicAttachment->getTopicId())->current();
            $first_name = $this->userMetaTable->getMetaByName($tutor, 'first_name')->current();
            $last_name = $this->userMetaTable->getMetaByName($tutor, 'last_name')->current();
            $topic_link = '<a href="/student/assignment/' . $work[0]['assignment'] . '">' . $topic->getName() . '</a>';
            $this->userActivityTable->add($worker, json_encode(['text' => $first_name->getValue() . ' ' . $last_name->getValue() . '  has provided feedback for <strong>' . $topic_link . '</strong>']));

            /**
             * @var User\Model\User $currentUser
             */
            $currentUser = $request->getAttribute(User\Model\User::class);

            /**
             * @var User\Model\User $receiver
             */
            $receiver = $this->userTable->oneById($worker);
            $first_name = $this->userMetaTable->getMetaByName($worker, 'first_name')->current();
            $last_name = $this->userMetaTable->getMetaByName($worker, 'last_name')->current();

            $tutor_first_name = $this->userMetaTable->getMetaByName($tutor, 'first_name')->current();
            $tutor_last_name = $this->userMetaTable->getMetaByName($tutor, 'last_name')->current();

            $courseContent = $this->contentTable->fetchByContentAndContentId($topicAttachment->getTopicId(), 'Topic')->current();
            $courseId = (int) $courseContent->getCourseId();
            $course = $this->courseTable->fetchById($courseId);

            $htmlMarkup = $this->template->render('emails::feedback', [
                'layout' => false,
                'tutorName' => $tutor_first_name->getValue() . ' ' . $tutor_last_name->getValue(),
                'studentName' => $first_name->getValue() . ' ' . $last_name->getValue(),
                'module' => $topic->getName(),
                'courseName' => $course->getName()
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
                    ->setSubject('Your feedback is now available!')
                    ->setBody($body);
                $this->transportMail->send($message);
            }
        }
        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('tutor/assignment/view/work/resultset', ['filter' => 'due'])
        ]);
    }
}