<?php

namespace User\Factory\Authentication;

use Psr\Container\ContainerInterface;
use User\Model;
use Zend\Authentication;

class AuthenticationService
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $storage = $container->get(Authentication\Storage\Session::class);

        /**
         * @var Model\UserTableGateway $userTableGateway
         */
        $userTableGateway = $container->get(Model\UserTableGateway::class);

        /**
         * @var Authentication\Adapter\DbTable\CredentialTreatmentAdapter $adapter
         */
        $adapter = $container->get(Authentication\Adapter\DbTable\CallbackCheckAdapter::class);
        $adapter->setTableName($userTableGateway->getTable());

        return new Authentication\AuthenticationService($storage, $adapter);
    }
}
