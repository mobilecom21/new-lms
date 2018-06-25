<?php

namespace Certificate\Action\View;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course;
use User\Model\UserTable;
use Exam;
use Attempt;

class ResultSet
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CertificatePaymentTable
     */
    private $paymentTable;

    /**
     * @var Model\CertificateDeliveryTable
     */
    private $deliveryTable;

    /**
     * @var Model\CourseTable
     */
    private $courseTable;

    /**
     * @var Model\userTable
     */
    private $userTable;

    /**
     * @var Model\examTriesTable
     */
    private $examTriesTable;

    /**
     * @var AttemptTable;
     */
    private $attemptTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\CertificatePaymentTable $paymentTable, Model\CertificateDeliveryTable $deliveryTable, Course\Model\CourseTable $courseTable, UserTable $userTable, Exam\Model\ExamTriesTable $examTriesTable, Attempt\Model\AttemptTable $attemptTable)
    {
        $this->template = $template;
        $this->paymentTable = $paymentTable;
		$this->deliveryTable = $deliveryTable;
		$this->courseTable = $courseTable;
		$this->userTable = $userTable;
		$this->examTriesTable = $examTriesTable;
		$this->attemptTable = $attemptTable;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return new HtmlResponse($this->template->render('certificate::resultset', [
            'resultSet' => $this->paymentTable->fetchAllToArray(),
			'course' => $this->courseTable,
			'user' => $this->userTable,
			'examtries' => $this->examTriesTable,
			'attempt' => $this->attemptTable,
			'delivery' => $this->deliveryTable,
        ]));
    }
}
