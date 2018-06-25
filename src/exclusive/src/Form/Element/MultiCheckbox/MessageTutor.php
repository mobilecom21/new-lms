<?php

namespace Exclusive\Form\Element\MultiCheckbox;

use Exclusive\Model;
use Zend\Form\Element\MultiCheckbox;
use Student;

class MessageTutor extends MultiCheckbox
{
    public function __construct(Model\MessageTutorTable $messageTutorTable, $name = 'messagetutor', array $options = [])
    {
        if (empty($options)) {
            $options = [
                'empty_option' => '',
                'label' => ' '
            ];
        }
		//parent::__construct($name, $options);  

    }
}
