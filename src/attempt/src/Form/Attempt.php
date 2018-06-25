<?php

namespace Attempt\Form;

use Attempt\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Attempt extends Form
{
    public function __construct($name = 'attempt', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Attempt);

        $this->add([
            'name' => 'studentId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'parentId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'submit_answers',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Click to submit your answers',
            ],
        ]);
    }
}
