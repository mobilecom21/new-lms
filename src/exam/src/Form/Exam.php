<?php

namespace Exam\Form;

use Exam\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Exam extends Form
{
    public function __construct($name = 'exam', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Exam);

        $this->add([
            'name' => 'courseId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'adminId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'parentId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'uploads',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'name',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Exam Name'
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save and return to course',
            ],
        ]);
    }
}
