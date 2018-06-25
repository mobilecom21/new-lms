<?php

namespace Message\InputFilter;

use Zend\InputFilter\InputFilter;

class Quiz extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'is_course',
            'required' => false
        ]);

        $this->add([
            'name' => 'is_content',
            'required' => false
        ]);

        $this->add([
            'name' => 'attachment',
            'required' => false
        ]);

        $this->add([
            'name' => 'text',
            'required' => true
        ]);
    }
}
