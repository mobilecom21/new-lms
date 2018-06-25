<?php

namespace Payment\Action\Form;

use Payment\Form;
use Payment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use User;
use Course;
use Options;

class Payment
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
     * @var Course\Model\Course
     */
	public $coursetable;

    /**
     * @var Options
     */
	public $optionsTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\PaymentTable $paymentTable, Course\Model\CourseTable $courseTable, Options\Model\OptionsTable $optionsTable)
    {
        $this->template = $template;
        $this->paymentTable = $paymentTable;
		$this->courseTable = $courseTable;
		$this->optionsTable = $optionsTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $courseId = $request->getAttribute('id');

        $form = new Form\Payment();

		$course = $this->courseTable->fetchById($courseId);
		$courseName = $course->getName();

        // populate adminId
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();
        $form->setData(['studentId' => $currentUserId]);

        // populate parentId
        $form->setData(['courseId' => $courseId]);

		$options = $this->optionsTable->fetchAll()->toArray();
		foreach ($options as $option) {
			if ('stripe_publishable_key' == $option['name']) {
				$stripe_publishable_key = $option['value'];
			}
			if ('stripe_secret_key' == $option['name']) {
				$stripe_secret_key = $option['value'];
			}
		}

        return new HtmlResponse($this->template->render('payment::form', [
            'form' => $form,
			'courseName' => $courseName,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key
        ]));
    }
}
