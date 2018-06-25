<?php

namespace Certificate\Action\Json;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use User;

class CertificateStopFirstOffer
{
    /**
     * @var Model\UserMetaTable
     */
    private $usermetaTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(User\Model\UserMetaTable $usermetaTable, UrlHelper $urlHelper)
    {
		$this->usermetaTable = $usermetaTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody();

        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();

		$getFirstOffer = $this->usermetaTable->getMetaByName($currentUserId,'no_first_offer')->current();

		if(!$getFirstOffer) {
			$addNoFirstOffer = $this->usermetaTable->add($currentUserId,'no_first_offer','yes');
		} else {
			$addNoFirstOffer = $this->usermetaTable->update($currentUserId,'no_first_offer','yes');
		}
		
        return new JsonResponse([
			"data" => $data
		]);
    }
}
