<?php

namespace Rbac\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rbac\Role;
use Zend\Expressive;
use Zend\Permissions;
use Zend\Diactoros\Response\RedirectResponse;

class Authorization implements MiddlewareInterface
{
    /**
     * @var Permissions\Rbac\Rbac
     */
    private $rbac;

    /**
     * @var Expressive\Helper\UrlHelper
     */
    private $urlHelper;

    public function __construct(Permissions\Rbac\Rbac $rbac, Expressive\Helper\UrlHelper $urlHelper)
    {
        $this->rbac = $rbac;
        $this->urlHelper = $urlHelper;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var Expressive\Router\RouteResult $routeResult
         */
        $routeResult = $request->getAttribute(Expressive\Router\RouteResult::class);

        if (!$routeResult) {
            return $delegate->process($request);
        }

        /**
         * @var string $matchedRouteName
         */
        $matchedRouteName = $routeResult->getMatchedRouteName();

        /**
         * @var string
         */
        $role = $request->getAttribute(Permissions\Rbac\Rbac::class, Role\Anonymous::class);

        if (!$this->rbac->isGranted($role, $matchedRouteName)) {
            if (Role\Anonymous::class == $role) {
                return new RedirectResponse(($this->urlHelper)('user/form/login'));
            }
            return $delegate->process($request->withAttribute(Expressive\Router\RouteResult::class, false));
        }

        return $delegate->process($request->withAttribute(self::class, true));
    }
}
