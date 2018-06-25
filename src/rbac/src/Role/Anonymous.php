<?php

namespace Rbac\Role;

use Zend\Permissions\Rbac\AbstractRole;

class Anonymous extends AbstractRole
{
    protected $name = self::class;
}
