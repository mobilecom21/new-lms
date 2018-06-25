<?php

namespace Course\Form;

use Course\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Course extends Form
{
    public function __construct($name = 'course', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Course);

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'name',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Course name',
            ],
        ]);

        $this->add([
            'name' => 'summary',
            'type' => Element\Textarea::class,
            'attributes' => [
                'rows' => 10
            ],
            'options' => [
                'label' => 'Course summary',
            ],
        ]);

        $this->add([
            'name' => 'tiny-image',
            'type' => Element\File::class,
            'attributes' => [
                'id' => 'tiny-image',
                'style' => 'display: none'
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Save and return to course',
            ],
        ]);
    }
}
