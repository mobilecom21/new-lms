<?php

namespace Tutor\User\Form\Element\Select;

use Zend\Form\Element\Select;

class Tutor extends Select
{
    public function __construct($name = 'tutor', array $options = [])
    {
        if (empty($options)) {
            $options = [
                'empty_option' => '',
                'label' => 'Tutor'
            ];
        }
        parent::__construct($name, $options);

        $this->setAttribute('required', 'required');
        $this->setAttribute('class', 'selectpicker show-tick');
        $this->setAttribute('data-live-search', 'true');
        $this->setAttribute('data-icon-base', 'fa');
        $this->setAttribute('data-tick-icon', 'fa-check');
        $this->setAttribute('data-none-selected-text', '');
    }
}
