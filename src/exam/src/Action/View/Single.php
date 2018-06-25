<?php

namespace Exam\Action\View;

//use Exam\Form;
use Exam\Model;
use User\Model\User;
use Tutor\Model\TutorStudentCourseTable;
use Topic\Model\AttachmentTable;
use Course\Model\ContentTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Uploader\Uploader;
use Zend\Form\Element;
use Zend\Form\Form;
use Attempt;
use Attempt\Model\AttemptTable;
use Zend\Expressive\Helper\UrlHelper;
use Exam\Model\ExamTriesTable;
use Certificate\Model\CertificatePaymentTable;
use User\Model\UserMetaTable;
use Zend\Session\Container;

class Single
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\ExamTable
     */
    private $examTable;

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
     * @var AttemptTable;
     */
    private $attemptTable;

    /**
     * @var ExamTriesTable;
     */
    private $examTriesTable;

    /**
     * @var CertificatePaymentTable;
     */
    private $certificatePaymentTable;

    /**
     * @var Model\UserMetaTable
     */
    private $usermetaTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Template\TemplateRendererInterface $template,
                                Model\ExamTable $examTable,
                                TutorStudentCourseTable $tutorStudentCourseTable,
                                AttachmentTable $attachmentTable,
                                ContentTable $contentTable,
                                Uploader $uploader,
                                AttemptTable $attemptTable,
                                ExamTriesTable $examTriesTable,
                                CertificatePaymentTable $certificatePaymentTable,
                                UserMetaTable $usermetaTable,
                                UrlHelper $urlHelper)
    {
        $this->template = $template;
        $this->examTable = $examTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->attachmentTable = $attachmentTable;
        $this->contentTable = $contentTable;
        $this->uploader = $uploader;
        $this->attemptTable = $attemptTable;
        $this->examTriesTable = $examTriesTable;
        $this->certificatePaymentTable = $certificatePaymentTable;
        $this->usermetaTable = $usermetaTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $attachment = $this->attachmentTable->fetchByAttachmentAndAttachmentId($id, 'Exam')->current();
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

        $exam = $this->examTable->fetchById($id)->current();
        $uploads = $exam->getUploads();
        $uploads = json_decode($uploads);
        $uploads = (Array)$uploads;
        $upload_id = reset($uploads);
        $upload = $this->uploader->get([$upload_id]);
        $upload = $upload[0] ?? [];
        if (! empty($upload['path']) && file_exists('data/media' . $upload['path'])) {
            $disposition = 'application/pdf' == $upload['type'] ? 'inline' : 'attachment';
            $file = 'data/media' . $upload['path'];
        }

        $attempts = $this->attemptTable->fetchByStudentIdExamId($currentUserId,$id);
        $numberattempt = count($attempts);

        //var_dump($attempts);
        if ($numberattempt > 0) {
            foreach($attempts ?? [] as $key => $attempt) {
                //var_dump($attempt);
                $score[$key] = $attempt->score;
                $theid[$key] = $attempt->id;
            }
            $maximum_score = max($score);
            $thekey = array_search($maximum_score,$score);
            $attemptid = $theid[$thekey];
            $thecertificateurl = $this->urlHelper->generate('student/attempt/view/certificate', ['id' => $attemptid]);
        } else {
            $maximum_score = 0;
            $thecertificateurl = false;
        }

        if($maximum_score >= 75) {
            $isPassed = true;
        } else {
            $isPassed = false;
        }


        $form = new Attempt\Form\Attempt();

        // bind object when id passed in url
        if (isset($exam) && $exam instanceof Model\Exam) {
            $form->bind($exam);
        }

        // populate userId
        $form->setData(['studentId' => $currentUserId]);

        // populate parentId
        $form->setData(['parentId' => $id]);

        //$data = $request->getParsedBody() ?? [];
        $previd = $_GET['previd'] ?? false;

        if ($previd) {
            $attempts = $this->attemptTable->fetchById($previd)->current();
            $score = $attempts->getScore();
        } else {
            $score = 0;
        }

        $certificatepayment = $this->certificatePaymentTable->fetchByStudentIdExamId($currentUserId,$id);
        $isCertificatePaymentExist = false;
        if($certificatepayment) {
            $countpayment = count($certificatepayment);
            if ($countpayment > 0) {
                $isCertificatePaymentExist = true;
            }
        }

        //$courseid = $this->examTriesTable->GetCourseIdfromExamId($id);
        $isCourseNoLimit = $this->examTriesTable->isCourseNoLimit($courseId,$currentUserId);
        $latestAttemptDateTime = $this->attemptTable->latestAttemptDateTime($currentUserId,$id);

        $questions = [];
        if (! empty($upload['path'])) {
            $file = $this->uploader->download($upload['path']);
            $lines = explode(PHP_EOL, $file->getContent());
            foreach ($lines as $line) {
                if (! empty($line)) {
                    $questions[] = str_getcsv($line);
                }
            }
        }

        return new HtmlResponse($this->template->render('exam::single', [
            'questions' => $questions,
            'file' => $this->examTable->fetchById($id)->current(),
            'single' => $this->examTable->fetchById($id),
            'courseId' => $courseId,
            'form' => $form,
            'isPassed' => $isPassed,
            'numberattempt' => $numberattempt,
            'highestscore' => $maximum_score,
            'score' => $score,
            'previd' => $previd,
            'certificate' => $thecertificateurl,
            'isCourseNoLimit' => $isCourseNoLimit,
            'latestAttemptDateTime' => $latestAttemptDateTime,
            'parentId' => $id,
            'isCertificatePaymentExist' => $isCertificatePaymentExist,
            'usermetaTable' => $this->usermetaTable
        ]));
    }
}
