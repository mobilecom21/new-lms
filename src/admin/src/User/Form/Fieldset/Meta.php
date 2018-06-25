<?php

namespace Admin\User\Form\Fieldset;

use User\Form\Element\FirstName;
use User\Form\Element\LastName;
use User\Form\Element\EmailAddress;
use User\Form\Element\Phone;
use Zend\Form\Fieldset;

class Meta extends Fieldset
{
    public function __construct($name = 'meta', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(new FirstName);
        $this->add(new LastName);
        $this->add(new Phone);
    }
}
