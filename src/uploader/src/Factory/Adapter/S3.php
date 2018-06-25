<?php

namespace Uploader\Factory\Adapter;

use Aws\S3\S3Client;
use Options\Model\OptionsTable;
use Psr\Container\ContainerInterface;

class S3
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new \Uploader\Adapter\S3($container->get(S3Client::class), $container->get(OptionsTable::class));
    }
}