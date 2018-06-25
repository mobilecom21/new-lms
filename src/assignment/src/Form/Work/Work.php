<?php

namespace Assignment\Form\Work;

use Assignment\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Work extends Form
{
    public function __construct($name = 'work', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Work\Work);

        $this->add([
            'name' => 'assignment',
            'type' => Element\Hidden::class,
            'attributes' => [
                'required' => true,
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Send for marking',
            ],
        ]);
    }
}
