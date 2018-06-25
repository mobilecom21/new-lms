<?php

use Zend\Session\Validator;

$time = 30 * 86400;

return [
    'session_config' => [
        'use_cookies' => true,
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'gc_maxlifetime' => $time,
        'cookie_lifetime' => $time
    ],
    'session_manager' => [
        'validators' => [
            Validator\RemoteAddr::class,
            // Validator\HttpUserAgent::class
        ]
    ],
    'session_storage' => [
        'type' => Zend\Session\Storage\SessionArrayStorage::class
    ]
];
