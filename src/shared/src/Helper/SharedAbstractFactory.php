<?php

namespace Shared\Helper;

use Interop\Container\ContainerInterface;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class SharedAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $cache;

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (0 !== strpos($requestedName, 'shared')) {
            return false;
        }

        if ($this->cache[$requestedName] ?? false) {
            return true;
        }

        $config = $container->get('config');

        /**
         * @var FilterChain $filterChain
         */
        $filterChain = $container->get(FilterChain::class);
        $filterChain->attach(new CamelCaseToDash);
        $filterChain->attach(new StringToLower);

        $requestedNameFiltered = $filterChain->filter($requestedName);
        $arrayName = explode('-', $requestedNameFiltered);

        foreach ($arrayName as $name) {
            $config = $config[$name] ?? [];
        }

        $this->cache[$requestedName] = $config;

        return !empty($config);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return function (bool $isService = true) use ($container, $requestedName) {
            foreach ($this->cache[$requestedName] as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                }
                $result[$key] = $isService ? $container->get($value) : $value;
            }

            return $result ?? [];
        };
    }
}
