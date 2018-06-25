<?php

namespace Course\Action\View\Single;

use Course\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Exclusive\Model\MessageTutorTable;
use Exclusive\Model\CertificatePrintFreeTable;
use User\Model\UserMetaTable;
use Exam\Model\ExamTable;

class ByUserId
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CourseUserTable
     */
    private $courseUserTable;

    /**
     * @var Model\ContentTable
     */
    private $contentTable;

    /**
     * @var Model\MessageTutorTable
     */
    private $messageTutorTable;

    /**
     * @var Model\CertificatePrintFreeTable
     */
    private $certificatePrintFreeTable;

    /**
     * @var Model\UserMetaTable
     */
    private $usermetaTable;

    /**
     * @var Model\ExamTable
     */
    private $examTable;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $routeNamespace;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\CourseUserTable $courseUserTable,
        Model\ContentTable $contentTable,
        MessageTutorTable $messageTutorTable,
        CertificatePrintFreeTable $certificatePrintFreeTable,
        UserMetaTable $usermetaTable,
        ExamTable $examTable,
        int $userId,
        bool $onlyRead = false,
        string $templateNamespace = null,
        string $routeNamespace = null
    ) {

        $this->template = $template;
        $this->courseUserTable = $courseUserTable;
        $this->contentTable = $contentTable;
        $this->messageTutorTable = $messageTutorTable;
        $this->certificatePrintFreeTable = $certificatePrintFreeTable;
        $this->usermetaTable = $usermetaTable;
        $this->examTable = $examTable;
        $this->userId = $userId;
        $this->routeNamespace = $routeNamespace;


        $this->templateName = $this->prepareTemplateName($onlyRead, $templateNamespace);
    }

    protected function prepareTemplateName(?bool $onlyRead, ?string $templateNamespace): string
    {
        return ($templateNamespace ? $templateNamespace . '::course/single/' : 'course::single/') . ($onlyRead ? 'read' : 'write');
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var string
         */
        $id = $request->getAttribute('id');

        /**
         * @var Model\Course $course
         */
        $course = $this->courseUserTable->oneByCourseIdAndUserId($id, $this->userId);

        if (false === $course) {
            return $delegate->process($request);
        }

        $currentUserId = $this->userId;
        $getFirstOffer = $this->usermetaTable->getMetaByName($currentUserId,'no_first_offer')->current();
        if($getFirstOffer) {
            $noFirstoffer = $getFirstOffer->getValue();
        } else {
            $noFirstoffer = "no";
        }

        $getShowOffer = $this->usermetaTable->getMetaByName($currentUserId,'show_offer')->current();
        if($getShowOffer) {
            $showOffer = $getShowOffer->getValue();
        } else {
            $showOffer = "yes";
        }

        $examIds = array();

        $n = 0;
        $contents = $this->contentTable->fetchByCourseId($id);
        if(count($contents) > 0) {
            foreach ($contents as $content) {
                $topicId = $content->getContentId();
                $exams = $this->examTable->fetchByTopicId($topicId);
                if(count($exams) > 0) {
                    foreach ($exams as $exam) {
                        $examIds[$n] = $exam->getId();
                        $n++;
                    }
                }
            }
        }

        $examNumber = count($examIds);

        $data['noFirstoffer'] = $noFirstoffer;
        $data['showOffer'] = $showOffer;
        $data['examNumber'] = $examNumber;
        $data['examIds'] = $examIds;

        return new HtmlResponse($this->template->render($this->templateName, [
            'routeNamespace' => $this->routeNamespace,
            'course' => $course,
            'content' => $this->contentTable->fetchByCourseId($id),
            'messagetutor' => $this->messageTutorTable,
            'certificateprintfree' => $this->certificatePrintFreeTable,
            'data' => $data
        ]));
    }
}
