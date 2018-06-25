<?php

namespace Student\User\InputFilter;

use User\InputFilter\User;

class Student extends User
{
    public function __construct()
    {
        parent::__construct();

        $this->add([], 'meta');
        $this->add([], 'courseTutor');
        /*
        $this->add([
            'name' => 'courseTutor\\[0\\]\\[home_learning_number\\]',
            'required' => true
        ]);

        $this->add([
            'name' => 'courseTutor\\[0\\]\\[order_number\\]',
            'required' => true
        ]);
        */
    }
}
