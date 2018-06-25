<?php

namespace Message\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Quiz extends Form
{
    public function __construct($name = 'quiz', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'content',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Which module:',
                'empty_option' => 'Choose',
            ],
            'attributes'=> [
                'hidden' => true
            ]
        ]);

        $this->add([
            'name' => 'is_course',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Is your enquiry related to your course?',
                'empty_option' => 'Choose',
                'value_options' => [
                    1 => 'Yes',
                    2 => 'No'
                ]
            ]
        ]);

        $this->add([
            'name' => 'is_content',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Is your enquiry related to one of your modules?',
                'empty_option' => 'Choose',
                'value_options' => [
                    1 => 'Yes',
                    2 => 'No'
                ]
            ],
            'attributes'=> [
                'hidden' => true
            ]
        ]);

        $this->add([
            'name' => 'attachment',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Which of the following does this relate to:',
                'empty_option' => 'Choose',
                'value_options' => [
                    'Assignment Feedback' => 'Assignment Feedback',
                    'Assignment Uploading' => 'Assignment Uploading',
                    'Underpinning Knowledge' => 'Underpinning Knowledge',
                    'Other' => 'Other'
                ]
            ],
            'attributes'=> [
                'hidden' => true
            ]
        ]);

        $this->add([
            'name' => 'text',
            'type' => Element\Textarea::class,
            'attributes' => [
                'placeholder' => 'Type your message here...',
                'hidden' => true,
                'required' => true
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Submit',
                'hidden' => true
            ],
            'hidden' => true
        ]);
    }
}