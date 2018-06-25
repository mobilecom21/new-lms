<?php

namespace ErrorLogger;

use Psr\Container\ContainerInterface;
use Zend\Log;

class ZendLoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $logger = new Log\Logger;
        $writer = new Log\Writer\Stream('data/errors.log');

        $logger->addWriter($writer);
        return new Log\PsrLoggerAdapter($logger);
    }
}