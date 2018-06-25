<?php

namespace Payment\Form;

use Payment\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Payment extends Form
{
    public function __construct($name = 'payment', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Payment);

        $this->add([
            'name' => 'courseId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'studentId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'itemname',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'No Limit Exam'
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Submit Payment',
            ],
        ]);
    }
}
