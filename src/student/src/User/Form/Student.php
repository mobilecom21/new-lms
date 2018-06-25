<?php

namespace Student\User\Form;

use Rbac\Role;
use Student\User\Form\Fieldset;
use User\Form\User;
use Zend\Form\Element;
use Zend\Form\Element\Collection;

class Student extends User
{
    public function __construct(
        Fieldset\CourseTutor $courseTutor,
        string $action,
        $name = 'student',
        array $options = []
    ) {
        parent::__construct($name, $options);

        // form action
        $this->setAttribute('action', $action);

        // fieldset
        $this->add(new Fieldset\Meta);
        $this->add([
            'type' => Collection::class,
            'name' => 'courseTutor',
            'options' => [
                'target_element' => $courseTutor,
                'allow_add' => true,
                'should_create_template' => true,
                'count' => 1
            ]
        ]);

        $this->setData([
            'role' => Role\Student::class
        ]);
    }
}
