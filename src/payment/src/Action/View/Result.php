<?php

namespace Payment\Action\View;

use Payment\InputFilter;
use Payment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Exam;
use Options;

class Result
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

    public function __construct(Template\TemplateRendererInterface $template, Model\PaymentTable $paymentTable, Exam\Model\ExamTriesTable $examTriesTable, Options\Model\OptionsTable $optionsTable)
    {
        $this->template = $template;
        $this->paymentTable = $paymentTable;
		$this->examTriesTable = $examTriesTable;
		$this->optionsTable = $optionsTable;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Payment();
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

		$courseId = $data['courseId'];
		$token  = $data['stripeToken'];
		$email  = $data['stripeEmail'];

		$customer = \Stripe\Customer::create(array(
		  'email' => $email,
		  'source'  => $token
		));

		$charge = \Stripe\Charge::create(array(
		  'customer' => $customer->id,
		  'amount'   => 500,
		  'currency' => 'GBP'
		));

		//\Stripe\Stripe::setApiKey($stripe['secret_key']);
		//$result = \Stripe\Charge::retrieve($charge);
		
		$value = $filter->getValues();
		$studentId = $value['studentId'];
		$courseId = $value['courseId'];
        $save = $this->paymentTable->save($studentId,$courseId);
		$insert = $this->examTriesTable->save_single($studentId,$courseId);
		
		$text = 'Payment successfull. Your credit card was charged &#163;5.00!';

        return new HtmlResponse($this->template->render('payment::result', [
            'result' => $text,
			'courseId' => $courseId,
			//'result' => $result
        ]));
    }
}
