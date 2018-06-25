<?php

namespace Tutor\User\InputFilter;

use User\InputFilter\User;

class Tutor extends User
{
    public function __construct()
    {
        parent::__construct();

        $this->add([], 'meta');
        $this->add([
            'required' => true
        ], 'courses');
    }
}
