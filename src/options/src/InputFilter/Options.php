<?php

namespace Options\InputFilter;

use Zend\InputFilter\InputFilter;

class Options extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'site_url',
            'required' => false,
        ]);
        $this->add([
            'name' => 'site_name',
            'required' => false,
        ]);
        $this->add([
            'name' => 'from_email',
            'required' => false,
        ]);
        $this->add([
            'name' => 'student_welcome_message',
            'required' => false,
        ]);
        $this->add([
            'name' => 'tutor_welcome_message',
            'required' => false,
        ]);
        $this->add([
            'name' => 'admin_welcome_message',
            'required' => false,
        ]);
        $this->add([
            'name' => 'amazon_key',
            'required' => false,
        ]);
        $this->add([
            'name' => 'amazon_secret',
            'required' => false,
        ]);
        $this->add([
            'name' => 'amazon_bucket',
            'required' => false,
        ]);
        $this->add([
            'name' => 'stripe_publishable_key',
            'required' => false,
        ]);
        $this->add([
            'name' => 'stripe_secret_key',
            'required' => false,
        ]);
    }
}
