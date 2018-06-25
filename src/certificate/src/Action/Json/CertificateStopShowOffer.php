<?php

namespace Certificate\Action\Json;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use User;

class CertificateStopShowOffer
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

		$getShowOffer = $this->usermetaTable->getMetaByName($currentUserId,'show_offer')->current();

		if(!$getShowOffer) {
			$addNoShowOffer = $this->usermetaTable->add($currentUserId,'show_offer','no');
		} else {
			$addNoShowOffer = $this->usermetaTable->update($currentUserId,'show_offer','no');
		}
		
        return new JsonResponse([
			"data" => $data
		]);
    }
}
