<?php

namespace Exam\Form\Element\MultiCheckbox;

use Exam\Model;
use Zend\Form\Element\MultiCheckbox;
use Student;

class ExamTries extends MultiCheckbox
{
    public function __construct(Model\ExamTable $examTable, $name = 'exam', array $options = [])
    {
        if (empty($options)) {
            $options = [
                'empty_option' => '',
                'label' => 'Exam'
            ];
        }
		//parent::__construct($name, $options);   

    }
}
