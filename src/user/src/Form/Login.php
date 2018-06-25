<?php

namespace User\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Login extends Form
{
    public function __construct($name = 'login', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'identity',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Email'
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'pin',
            'type' => Element\Password::class,
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Sign In',
            ],
        ]);
    }
}
