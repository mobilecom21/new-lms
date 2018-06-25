<?php

namespace Rbac\Factory;

use Psr\Container\ContainerInterface;
use Zend\Permissions;

class Rbac
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')['rbac'];

        $rbac = new Permissions\Rbac\Rbac();

        /**
         * @var array
         */
        $roles = $config['roles'];

        /**
         * @var array
         */
        $permissions = $config['permissions'];

        foreach ($roles as $id => $children) {
            /**
             * @var Permissions\Rbac\Role $role
             */
            $role = $container->get($id);
            foreach ($permissions[$id] ?? [] as $permission) {
                $role->addPermission($permission);
            }

            foreach ($children as $child) {
                $role->addChild($rbac->getRole($child));
            }

            $rbac->addRole($role);
        }

        return $rbac;
    }
}
