<?php

/*
* Works out the subdomain (reseller url) so that we can llater route people appropriately,
* authenticate users against reselllers (which allows for duplicate email addresses in the user table)
*/

namespace User\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Session\Container;

class Reseller implements MiddlewareInterface
{


    public function __construct()
    {
        //intentionally blank
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        //fetch subdomain
        $url = parse_url($_SERVER['HTTP_HOST']);

        if(empty($url['host'])) {
            return $delegate->process($request);
        }

        $reseller = explode('.', $url['host'])[0];

        //add it to the resellerUrl session space
        $resellerUrl = new Container('resellerUrl');
        $resellerUrl->url = $reseller;

        return $delegate->process($request);
    }
}
