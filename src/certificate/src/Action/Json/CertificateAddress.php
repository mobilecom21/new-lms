<?php

namespace Certificate\Action\Json;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;

class CertificateAddress
{
    /**
     * @var Model\CertificateAddressTable
     */
    private $addressTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\CertificateAddressTable $addressTable, UrlHelper $urlHelper)
    {
		$this->addressTable = $addressTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody();

		$addressId = $this->addressTable->save($data);

		if(!$data['coupon']) {
			$data['coupon'] = NULL;
		}

		if(strpos($data['coupon'], 'freeprint') !== false) {
			$id = explode("_",$data['coupon']);
			return new JsonResponse([
				'redirectTo' => ($this->urlHelper)('attempt/view/success',['id' => $id[1]])
			]);
		}
		
        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('certificate/form/payment', ['id' => $data['exam_id'],'coupon' => $data['coupon']])
        ]);
    }
}
