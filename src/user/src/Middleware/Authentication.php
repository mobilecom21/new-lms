<?php

namespace User\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Model;
use Zend\Authentication as ZendAuthentication;
use Zend\Permissions;

class Authentication implements MiddlewareInterface
{
    /**
     * @var ZendAuthentication\AuthenticationService
     */
    private $authenticationService;

    public function __construct(ZendAuthentication\AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (!$this->authenticationService->hasIdentity()) {
            return $delegate->process($request);
        }

        /**
         * @var Model\User $user
         */
        $user = $this->authenticationService->getStorage()->read();

        $request = $request->withAttribute(Model\User::class, $user)
            ->withAttribute(Permissions\Rbac\Rbac::class, $user->getRole());

        return $delegate->process($request);
    }
}
