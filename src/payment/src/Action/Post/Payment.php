<?php

namespace Payment\Action\Post;

use Payment\InputFilter;
use Payment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Payment
{
    /**
     * @var Model\PaymentTable
     */
    private $paymentTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\PaymentTable $paymentTable, UrlHelper $urlHelper)
    {
        $this->paymentTable = $paymentTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Payment();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }
		$stripe = array(
		  "secret_key"      => "sk_test_yotgmmWn6jOwMfegMk0K9Gqu",
		  "publishable_key" => "pk_test_eXe77GO3ILFEiBbkHEivcQs4"
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
		  'amount'   => 50,
		  'currency' => 'usd'
		));

        //$save = $this->paymentTable->save($filter->getValues());

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('student/payment/view/result', ['id' => $data['courseId']])
        ]);
    }
}
