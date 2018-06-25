<?php

namespace Message\Action\Post;

use Message\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Viewed
{
    /**
     * @var User\Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\MessageTable
     */
    private $messageTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        Model\MessageTable $messageTable,
        User\Model\UserTable $userTable,
        UrlHelper $urlHelper
    )
    {
        $this->messageTable = $messageTable;
        $this->userTable = $userTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();

        $id = $request->getParsedBody()['id'] ?? 0;
        if ($id > 0) {
            $this->messageTable->viewed($id, $currentUserId);
        }

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('message/view/resultset')
        ]);
    }
}