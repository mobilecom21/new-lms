<?php

namespace Scorm\Form;

use Scorm\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Scorm extends Form
{
    public function __construct($name = 'scorm', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Scorm);

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
            'name' => 'summary',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Description',
            ],
            'attributes' => [
                'rows' => 10,
            ]
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
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save and return to course',
            ],
        ]);
    }
}
