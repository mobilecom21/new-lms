<?php

namespace Student\User\Form\Fieldset;

use User\Form\Element\Address;
use User\Form\Element\EmailAddress;
use User\Form\Element\FirstName;
use User\Form\Element\Hln;
use User\Form\Element\LastName;
use User\Form\Element\Phone;
use User\Form\Element\Pin;
use User\Form\Element\Suspended;
use User\Form\Element\CertificateOffer;
use Zend\Form\Fieldset;

class Meta extends Fieldset
{
    public function __construct($name = 'meta', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(new FirstName);
        $this->add(new LastName);
        $this->add(new Phone);
        $this->add(new Address);
        $this->add(new Suspended);
        $this->add(new CertificateOffer);
    }
}
