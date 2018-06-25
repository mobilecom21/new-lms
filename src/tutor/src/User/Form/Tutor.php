<?php

namespace Tutor\User\Form;

use Course;
use Rbac\Role;
use Tutor\User\Form\Fieldset;
use User\Form\User;
use Zend\Form\Element;

class Tutor extends User
{
    public function __construct(Course\Form\Element\Select\Course $courseSelectElement, string $action, $name = 'tutor', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('action', $action);

        $this->add(new Fieldset\Meta);
        $this->add($courseSelectElement);

        $this->setData([
            'role' => Role\Tutor::class
        ]);
    }
}
