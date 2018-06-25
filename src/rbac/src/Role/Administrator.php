<?php

namespace Rbac\Role;

use Zend\Permissions\Rbac\AbstractRole;

class Administrator extends AbstractRole
{
    protected $name = self::class;
}
