<?php

namespace User\Form;

use User\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class User extends Form
{
    public function __construct($name = 'user', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setInputFilter(new InputFilter\User);

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'role'
        ]);

        $this->add([
            'name' => 'username',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Username'
            ],
        ]);

        $this->add([
            'name' => 'assign-username',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Auto Assign Username'
            ],
            'attributes' => [
                'class' => 'btn-warning btn'
            ]
        ]);

        $this->add([
            'name' => 'identity',
            'type' => Element\Email::class,
            'options' => [
                'label' => 'Email'
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'assign-password',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Auto Assign Password'
            ],
            'attributes' => [
                'class' => 'btn-warning btn'
            ]
        ]);

        $this->add([
            'name' => 'assign-pin',
            'type' => Element\Button::class,
            'options' => [
                'label' => 'Auto Assign PIN'
            ],
            'attributes' => [
                'class' => 'btn-warning btn'
            ]
        ]);

        $this->add([
            'name' => 'pin',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Pin',
            ],
        ]);

        $this->add([
            'name' => 'plainpin',
            'type' => Element\Hidden::class,
        ]);

        $this->add([
            'name' => 'notify-user',
            'type' => Element\Checkbox::class,
            'attributes' => [
                'value' => 1,
                'style' => 'width: 25px; cursor: pointer;'
            ],
            'options' => [
                'label' => 'Send Notification Email',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save',
            ],
        ]);
    }
}
