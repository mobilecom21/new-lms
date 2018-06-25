<?php

namespace Certificate\Action\Post;

use Certificate\InputFilter;
use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Exam;
use Options;
use Zend\Session\Container;

class CertificatePayment
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
     * @var ExamTries\Model\ExamTriesTable
     */
    protected $examTriesTable;

    /**
     * @var Options
     */
	public $optionsTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\CertificatePaymentTable $paymentTable, Exam\Model\ExamTriesTable $examTriesTable, Options\Model\OptionsTable $optionsTable)
    {
        $this->template = $template;
        $this->paymentTable = $paymentTable;
		$this->examTriesTable = $examTriesTable;
		$this->optionsTable = $optionsTable;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

		$session = new Container('offers15');
		if($session->offsetExists('coupon')) {
			$session->offsetUnset('coupon');
		}

        $filter = new InputFilter\CertificatePayment();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new HTMLResponse([
                'result' => $filter->getMessages()
            ]);
        }

		$options = $this->optionsTable->fetchAll()->toArray();
		foreach ($options as $option) {
			if ('stripe_publishable_key' == $option['name']) {
				$stripe_publishable_key = $option['value'];
			}
			if ('stripe_secret_key' == $option['name']) {
				$stripe_secret_key = $option['value'];
			}
		}

		$stripe = array(
		  "secret_key"      => $stripe_secret_key,
		  "publishable_key" => $stripe_publishable_key
		);

		\Stripe\Stripe::setApiKey($stripe['secret_key']);

		//$courseId = $data['course_id'];
		$token  = $data['stripeToken'];
		$email  = $data['stripeEmail'];
		$coupon = $data['coupon'];

		$customer = \Stripe\Customer::create(array(
		  'email' => $email,
		  'source'  => $token
		));
	
		if ($coupon) {
			$price = 1500;
			$dprice = '15.00';
		} else {
			$price = 2500;
			$dprice = '25.00';
		}
		$charge = \Stripe\Charge::create(array(
		  'customer' => $customer->id,
		  'amount'   => $price,
		  'currency' => 'GBP'
		));

		//\Stripe\Stripe::setApiKey($stripe['secret_key']);
		//$result = \Stripe\Charge::retrieve($charge);
		
		$value = $filter->getValues();
		$studentId = $value['student_id'];
		$examId = $value['exam_id'];
		$courseId = $this->examTriesTable->GetCourseIdfromExamId($examId);
        $save = $this->paymentTable->save($value);
		
		$text = 'Payment successfull. Your credit card was charged &#163;'.$dprice.'!';

        return new HtmlResponse($this->template->render('certificate::result', [
            'result' => $text,
			'courseId' => $courseId,
			//'result' => $result
        ]));
    }
}
