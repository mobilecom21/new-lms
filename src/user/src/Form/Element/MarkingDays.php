<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class MarkingDays extends Text
{
    public function __construct($name = 'marking_days', array $options = ['label' => 'Marking Days'])
    {
        parent::__construct($name, $options);
    }
}
