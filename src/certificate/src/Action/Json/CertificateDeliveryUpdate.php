<?php

namespace Certificate\Action\Json;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Attempt;

class CertificateDeliveryUpdate
{
    /**
     * @var Certificate\Model\CertificateDeliveryTable
     */
    private $deliveryTable;

    /**
     * @var Certificate\Model\CertificatePaymentTable
     */
    private $paymentTable;

    /**
     * @var Attemt\Model\AttemptTable
     */
    private $attemptTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\CertificateDeliveryTable $deliveryTable, Model\CertificatePaymentTable $paymentTable, Attempt\Model\AttemptTable $attemptTable, UrlHelper $urlHelper)
    {
		$this->deliveryTable = $deliveryTable;
		$this->paymentTable = $paymentTable;
		$this->attemptTable = $attemptTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody();
		$id = $request->getAttribute('id');		

		$paymentId = $id;
		$input['payment_id'] = $paymentId;

		$fetchPayment = $this->deliveryTable->fetchByPaymentId($paymentId)->current();
		
		if($fetchPayment) {
			$StatusSent = $fetchPayment->getStatusSent();
			$input['id'] = $fetchPayment->getId();
			if($StatusSent) {
				$input['status_sent'] = 0;
			} else {
				$input['status_sent'] = 1;
			}
			
		} else {
			$input['status_sent'] = 1;			
			$input['id'] = null;
		}

		$UpdateStatusSent = $this->deliveryTable->save($input);
		
        return new RedirectResponse(($this->urlHelper)('certificate/view/resultset'));
    }
}
