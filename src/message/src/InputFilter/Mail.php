<?php

namespace Message\InputFilter;

use Zend\InputFilter\InputFilter;

class Mail extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'text',
            'required' => true
        ]);
    }
}
