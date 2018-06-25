<?php

namespace Mail\Transport;

use Psr\Container\ContainerInterface;
use Zend\Mail\Transport;

class Factory extends Transport\Factory
{
    protected $classMapExtended = [
    ];

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mailConfig = $container->get('config')['mail'] ?? [];

        $this->extendClassMap();
        return self::create($mailConfig);
    }

    protected function extendClassMap()
    {
        foreach ($this->classMapExtended as $key => $value) {
            self::$classMap[$key] = $value;
        }
    }
}
