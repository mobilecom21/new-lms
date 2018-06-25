<?php

namespace Tutor\User\Form\Fieldset;

use User\Form\Element\AccountNumber;
use User\Form\Element\FirstName;
use User\Form\Element\LastName;
use User\Form\Element\EmailAddress;
use User\Form\Element\MarkingDays;
use User\Form\Element\PaymentMethod;
use User\Form\Element\Phone;
use User\Form\Element\Pin;
use User\Form\Element\SortCode;
use User\Form\Element\Suspended;
use Zend\Form\Fieldset;

class Meta extends Fieldset
{
    public function __construct($name = 'meta', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(new FirstName);
        $this->add(new LastName);
        $this->add(new Phone);
        $this->add(new Suspended);
        $this->add(new SortCode);
        $this->add(new AccountNumber);
        $this->add(new PaymentMethod);
        $this->add(new MarkingDays);
    }
}
