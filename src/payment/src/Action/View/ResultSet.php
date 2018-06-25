<?php

namespace Payment\Action\View;

use Payment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course;
use User\Model\UserTable;

class ResultSet
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\PaymentTable
     */
    private $paymentTable;

    /**
     * @var Model\CourseTable
     */
    private $courseTable;

    /**
     * @var Model\userTable
     */
    private $userTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\PaymentTable $paymentTable, Course\Model\CourseTable $courseTable, UserTable $userTable)
    {
        $this->template = $template;
        $this->paymentTable = $paymentTable;
		$this->courseTable = $courseTable;
		$this->userTable = $userTable;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return new HtmlResponse($this->template->render('payment::resultset', [
            'resultSet' => $this->paymentTable->fetchAllToArray(),
			'course' => $this->courseTable,
			'user' => $this->userTable
        ]));
    }
}
