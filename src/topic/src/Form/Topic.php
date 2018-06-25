<?php

namespace Topic\Form;

use Topic\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Topic extends Form
{
    public function __construct($name = 'topic', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Topic);

        $this->add([
            'name' => 'parentId',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'name',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Name',
                'label_attributes' => [
                    'class' => 'name'
                ]
            ],
        ]);

        $this->add([
            'name' => 'summary',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Summary',
            ],
            'attributes' => [
                'rows' => 10,
            ]
        ]);

        $this->add([
            'name' => 'required',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Required',
                'value_options' => [
                    1 => 'Yes',
                    0 => 'No'
                ]
            ],
        ]);

        $this->add([
            'name' => 'sort_order',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Sort Order',
                'label_attributes' => [
                    'class' => 'name'
                ]
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
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save and return to course',
            ],
        ]);
    }
}
