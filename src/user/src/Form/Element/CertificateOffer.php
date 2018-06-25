<?php

namespace User\Form\Element;

use Zend\Form\Element\Checkbox;

class CertificateOffer extends Checkbox
{
    public function __construct($name = 'show_offer', array $options = ['label' => 'Certificate Offer'])
    {
        parent::__construct($name, $options);
        $this->setUseHiddenElement(true);
        $this->setCheckedValue("yes");
        $this->setUncheckedValue("no");
        $this->setChecked(false);
    }
}