<?php

namespace Certificate\Action\Json;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Attempt;

class CertificateDeliverySent
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
		$ids = $data['id'];		

		if(count($ids) > 0) {
			foreach($ids as $dataId) {
				$inputId = explode("|",$dataId);
				$attemptId = $inputId[0];
				$paymentId = $inputId[1];
				
				//$attempt = $this->attemptTable->fetchById($attemptId)->current();
				//$examId = $attempt->getExamId();
				//$studentId = $attempt->getStudentId();
				//$fetchPayment = $this->paymentTable->fetchByStudentIdExamId($studentId,$examId);

				//$paymentId = $payment->getId();

				$StatusSent = $this->deliveryTable->fetchByPaymentId($paymentId)->current();
				$input['status_sent'] = 1;
				$input['payment_id'] = $paymentId;
				if($StatusSent) {
					$input['id'] = $StatusSent->getId();
				} else {
					$input['id'] = null;
				}

				$UpdateStatusSent = $this->deliveryTable->save($input);
			}
		} 
		
        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('certificate/view/resultset')
		]);
    }
}
