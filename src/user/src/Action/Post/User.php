<?php

namespace User\Action\Post;

use User\Model\UserTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use Assignment\Model\AssignmentWorkTable;
use Tutor\Model\TutorStudentCourseTable;
use Options;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;
use Course\Model\CourseTable;
use User\Model\UserMetaTable;

class User
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var CourseTable
     */
    private $courseTable;

    /**
     * @var AttachmentTable
     */
    private $attachmentTable;

    /**
     * @var ContentTable;
     */
    private $contentTable;

    /**
     * @var AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var Options
     */
    private $optionsTable;

    /**
     * @var InputFilterInterface
     */
    private $inputFilter;

    /**
     * @var string
     */
    private $successUrl;

    public function __construct(
        Template\TemplateRendererInterface $template,
        UserTable $userTable,
        UserMetaTable $userMetaTable,
        CourseTable $courseTable,
        AttachmentTable $attachmentTable,
        ContentTable $contentTable,
        AssignmentWorkTable $assignmentWorkTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        Options\Model\OptionsTable $optionsTable,
        InputFilterInterface $inputFilter,
        string $successUrl,
        Mail\Transport\TransportInterface $transportMail
    )
    {
        $this->template = $template;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->courseTable = $courseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->optionsTable = $optionsTable;
        $this->inputFilter = $inputFilter;
        $this->successUrl = $successUrl;
        $this->transportMail = $transportMail;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];
        $this->inputFilter->setData($data);
        if (!$this->inputFilter->isValid()) {
            return new JsonResponse([
                'errors' => $this->inputFilter->getMessages()
            ]);
        }

        if (! empty($data['id']) && !empty($data['courseTutor'])) {
            foreach ($data['courseTutor'] as $courseTutor) {
                $oldTutorRow = $this->tutorStudentCourseTable->fetchByStudentAndCourse($data['id'], $courseTutor['course'])->current();
                if ($oldTutorRow && $oldTutorRow->getTutorId()) {
                    $assignments =  $this->assignmentWorkTable->byWorkerAndTutor($data['id'], $oldTutorRow->getTutorId());
                    if (count($assignments) > 0) {
                        foreach ($assignments as $assignment) {
                            $attachment = $this->attachmentTable->fetchByAttachmentAndAttachmentId($assignment['id'], 'Assignment')->current();
                            if ($attachment) {
                                $topicId = $attachment->getTopicId();
                                $content = $this->contentTable->fetchByContentId($topicId)->current();
                                if ($content) {
                                    $courseId = $content->getCourseId();
                                    if ($courseId == $courseTutor['course']) {
                                        $this->assignmentWorkTable->updateTutor($assignment['id'], $courseTutor['tutor']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $response = $this->userTable->save($this->inputFilter->getValues());

        $notify = ! empty($data['notify-user']) ?? false;
        $email = $this->inputFilter->getValues()['identity'] ?? '';
        $password = $this->inputFilter->getValues()['password'] ?? '';
        $pin = $this->inputFilter->getValues()['pin'] ?? '';
        $firstName = $data['meta']['first_name'] ?? '';
        $lastName = $data['meta']['last_name'] ?? '';

        if ('duplicate_identity' === $response) {
            return new JsonResponse([
                'errors' => ['identity' => ['duplicate' => 'Email Already Exists!']]
            ]);
        } elseif ('duplicate_username' === $response) {
            return new JsonResponse([
                'errors' => ['username' => ['duplicate' => 'Username Already Exists!']]
            ]);
        } elseif ('duplicate_pin' === $response) {
            return new JsonResponse([
                'errors' => ['pin' => ['duplicate' => 'Pin Already Exists!']]
            ]);
        }

        if ($this->optionsTable->optionExists('from_email')) {
            $from = $this->optionsTable->fetchByName('from_email')['value'];
        }

        if ('Rbac\Role\Student' == $data['role'] && $notify && ! empty($email) && ! empty($password)) {
            $courseId = $data['courseTutor'][0]['course'] ?? 0;
            $courseName = '';
            if ($courseId > 0) {
                $course = $this->courseTable->fetchById($courseId);
                $courseName = $course->getName();
            }

            //send welcome message to student
            $htmlMarkup = $this->template->render('emails::newaccountstudent', [
                'layout' => false,
                'fullName' => $firstName . ' ' . $lastName,
                'username' => $this->inputFilter->getValues()['username'],
                'password' => $password,
                'pin' => $pin,
                'courseName' => $courseName
            ]);
            $html = new MimePart($htmlMarkup);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->addPart($html);

            if (! empty($from)) {
                $message = new Mail\Message();
                $message->addTo($email)
                    ->addFrom($from)
                    ->setSubject('Welcome to NCC Home Learning')
                    ->setBody($body);
                $this->transportMail->send($message);
            }

            //send message to tutor about new student added
            $tutorId = $data['courseTutor'][0]['tutor'] ?? 0;
            $receiver = $this->userTable->oneById($tutorId);
            $tutor_first_name = $this->userMetaTable->getMetaByName($tutorId, 'first_name')->current();
            $tutor_last_name = $this->userMetaTable->getMetaByName($tutorId, 'last_name')->current();
            $htmlMarkup = $this->template->render('emails::newstudentfortutor', [
                'layout' => false,
                'studentName' => $firstName . ' ' . $lastName,
                'tutorName' => $tutor_first_name->getValue() . ' ' . $tutor_last_name->getValue(),
                'pin' => $pin,
                'courseName' => $courseName
            ]);
            $html = new MimePart($htmlMarkup);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->addPart($html);

            if (! empty($from)) {
                $message = new Mail\Message();
                $message->addTo($receiver->getIdentity())
                    ->addFrom($from)
                    ->setSubject('You have a new student ' .  $firstName . ' ' . $lastName)
                    ->setBody($body);
                $this->transportMail->send($message);
            }

        } elseif ('Rbac\Role\Tutor' == $data['role'] && $notify && ! empty($email) && ! empty($password)) {
            $htmlMarkup = $this->template->render('emails::newaccounttutor', [
                'layout' => false,
                'fullName' => $firstName . ' ' . $lastName,
                'username' => $this->inputFilter->getValues()['username'],
                'password' => $password,
                'pin' => $pin
            ]);
            $html = new MimePart($htmlMarkup);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->addPart($html);

            if (! empty($from)) {
                $message = new Mail\Message();
                $message->addTo($email)
                    ->addFrom($from)
                    ->setSubject('Welcome to NCC Home Learning')
                    ->setBody($body);
                $this->transportMail->send($message);
            }
        } elseif ('Rbac\Role\Administrator' == $data['role'] && ! empty($email) && ! empty($password)) {
            $htmlMarkup = $this->template->render('emails::newaccountadmin', [
                'layout' => false,
                'fullName' => $firstName . ' ' . $lastName,
                'username' => $this->inputFilter->getValues()['username'],
                'password' => $password,
                'pin' => $pin
            ]);
            $html = new MimePart($htmlMarkup);
            $html->type = "text/html";

            $body = new MimeMessage();
            $body->addPart($html);

            if (! empty($from)) {
                $message = new Mail\Message();
                $message->addTo($email)
                    ->addFrom($from)
                    ->setSubject('Welcome to NCC Home Learning')
                    ->setBody($body);
                $this->transportMail->send($message);
            }
        }

        return new JsonResponse([
            'redirectTo' => ($this->successUrl)
        ]);
    }
}