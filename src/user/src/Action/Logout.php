<?php

namespace User\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template;
use Zend\Session\Container;

class Logout
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    public function __construct(Template\TemplateRendererInterface $template, UrlHelper $urlHelper, AuthenticationService $authenticationService)
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->authenticationService = $authenticationService;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $originalUser = new Container('OriginalUser');

        //Logout of all levels
        if('all' == $request->getAttribute('instruction')) {
            //no users above us in the chain, log out completely
            $originalUser->userChain = array();
            $originalUser->currentUser = array();
            $originalUser->topLevelRole = null;

            $this->authenticationService->clearIdentity();

            return new RedirectResponse(($this->urlHelper)('user/form/login'));
        }


        //find out if we're a "Login As" candidate
        $count = count($originalUser->userChain);
        if($count > 0) {
            $newUser = $originalUser->userChain[$count-1];
            array_pop($originalUser->userChain);

            $originalUser->currentUser = $newUser;

            $this->authenticationService->clearIdentity();
            $storage = $this->authenticationService->getStorage();
            $storage->write($newUser);
            return new RedirectResponse(($this->urlHelper)('home'));
        }

        //no users above us in the chain, log out completely
        $originalUser->userChain = array();
        $originalUser->currentUser = array();
        $originalUser->topLevelRole = null;

        $this->authenticationService->clearIdentity();

        return new RedirectResponse(($this->urlHelper)('user/form/login'));
    }
}
