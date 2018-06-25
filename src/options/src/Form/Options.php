<?php

namespace Options\Form;

use Options\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class Options extends Form
{
    public function __construct($name = 'options', array $options = [])
    {

        #$v = new \EventLogger\Helper\EventLogger;

        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\Options);

        $this->add([
            'name' => 'site_url',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Site Url',
            ],
        ]);

        $this->add([
            'name' => 'site_name',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Site Name',
            ],
        ]);

        $this->add([
            'name' => 'from_email',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'From Email',
            ],
        ]);

        $this->add([
            'name' => 'admin_welcome_message',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Admin Welcome Message',
            ],
        ]);

        $this->add([
            'name' => 'tutor_welcome_message',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Tutor Welcome Message',
            ],
        ]);

        $this->add([
            'name' => 'student_welcome_message',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Student Welcome Message',
            ],
        ]);

        $this->add([
            'name' => 'amazon_key',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Amazon Key',
            ],
        ]);

        $this->add([
            'name' => 'amazon_secret',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Amazon Secret',
            ],
        ]);

        $this->add([
            'name' => 'amazon_bucket',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Amazon Bucket',
            ],
        ]);

        $this->add([
            'name' => 'stripe_publishable_key',
            'type' => Element::class,
            'options' => [
                'label' => 'Stripe Publishable key',
            ],
        ]);

        $this->add([
            'name' => 'stripe_secret_key',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Stripe Secret Key',
            ],
        ]);

        $this->add([
            'name' => 'tiny-image',
            'type' => Element\File::class,
            'attributes' => [
                'id' => 'tiny-image',
                'style' => 'display: none'
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save',
            ],
        ]);
    }
}
