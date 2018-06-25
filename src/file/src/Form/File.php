<?php

namespace File\Form;

use File\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class File extends Form
{
    public function __construct($name = 'file', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\File);

        $this->add([
            'name' => 'courseId',
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
                'label' => 'Name'
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
