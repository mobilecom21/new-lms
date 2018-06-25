<?php

namespace Message\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Mail extends Form
{
    public function __construct($name = 'mail', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'text',
            'type' => Element\Textarea::class,
            'attributes' => [
                'placeholder' => 'Type your message here...',
                'required' => true
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Send'
            ]
        ]);
    }
}