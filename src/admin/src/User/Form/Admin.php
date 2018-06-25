<?php

namespace Admin\User\Form;

use Rbac\Role;
use Admin\User\Form\Fieldset;
use User\Form\User;

class Admin extends User
{
    public function __construct(string $action, $name = 'admin', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('action', $action);

        $this->add(new Fieldset\Meta);

        $this->setData([
            'role' => Role\Administrator::class
        ]);
    }
}
