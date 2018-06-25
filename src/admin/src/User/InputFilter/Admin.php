<?php

namespace Admin\User\InputFilter;

use User\InputFilter\User;

class Admin extends User
{
    public function __construct()
    {
        parent::__construct();

        $this->add([], 'meta');
    }
}
