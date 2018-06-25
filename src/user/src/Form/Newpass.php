<?php

namespace User\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Newpass extends Form
{
    public function __construct($name = 'login', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'New Password',
            ]
        ]);

        $this->add([
            'name' => 'vpassword',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Confirm New Password',
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Submit',
            ]
        ]);
    }
}
