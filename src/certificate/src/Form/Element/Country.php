<?php

namespace Certificate\Form\Element;

use Zend\Form\Element\Select;

class Country extends Select
{
    public function __construct($name = 'country', array $options = [])
    {
        if (empty($options)) {
            $options = [
                'empty_option' => '',
                'label' => 'Other'
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
