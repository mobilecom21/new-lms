<?php

namespace Exclusive\Form\Element\MultiCheckbox;

use Exclusive\Model;
use Zend\Form\Element\MultiCheckbox;
use Student;

class CertificatePrintFree extends MultiCheckbox
{
    public function __construct(Model\CertificatePrintFreeTable $certificatePrintFreeTable, $name = 'certificateprintfree', array $options = [])
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
