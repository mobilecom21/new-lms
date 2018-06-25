<?php

namespace Amazon\Factory;

use Psr\Container\ContainerInterface;
use Aws\S3\S3Client;
use Options\Model\OptionsTable;

class S3Factory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var OptionsTable $optionsTable
         */
        $optionsTable = $container->get(OptionsTable::class);
        return new S3Client([
            'version'     => 'latest',
            'region'      => 'eu-west-1',
            'credentials' => [
                'key' => $optionsTable->fetchByName('amazon_key')['value'] ?? '',
                'secret' => $optionsTable->fetchByName('amazon_secret')['value'] ?? '',
            ]
        ]);

       /*$awsConfig = $container->get('config')['aws'] ?? [];
        return new S3Client([
            'version'     => 'latest',
            'region'      => 'eu-west-1',
            'credentials' => $awsConfig['credentials']
        ]);*/
    }
}
