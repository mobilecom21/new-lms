<?php

namespace Assignment\Form\Work;

use Assignment\InputFilter;
use Assignment\Model\AssignmentWork;
use Zend\Form\Element;
use Zend\Form\Form;

class Feedback extends Form
{
    public function __construct($name = 'feedback', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Work\Feedback);

        $this->add([
            'name' => 'feedback',
            'type' => Element\Hidden::class,
            'attributes' => [
                'required' => true,
            ]
        ]);

        $failed = new Element\Select('failed');
        $failed->setLabel('Has this student met the required criteria to pass this module?');
        $failed->setValueOptions([
            '' => '',
            AssignmentWork::STATUS_FAIL => 'No',
            AssignmentWork::STATUS_PASS => 'Yes'
        ]);
        $failed->setAttribute('required', 'true');
        $this->add($failed);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Send feedback'
            ],
        ]);
    }
}
