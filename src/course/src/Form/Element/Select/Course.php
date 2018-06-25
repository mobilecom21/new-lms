<?php

namespace Course\Form\Element\Select;

use Course\Model;
use Zend\Form\Element\Select;

class Course extends Select
{
    public function __construct(Model\CourseTable $courseTable, $name = 'courses', array $options = [])
    {
        if (empty($options)) {
            $options = [
                'empty_option' => '',
                'label' => 'Course'
            ];
        }
        parent::__construct($name, $options);

        $this->setAttribute('multiple', 'multiple');
        $this->setAttribute('required', 'required');
        $this->setAttribute('class', 'selectpicker show-tick');
        $this->setAttribute('data-live-search', 'true');
        $this->setAttribute('data-icon-base', 'fa');
        $this->setAttribute('data-tick-icon', 'fa-check');
        $this->setAttribute('data-none-selected-text', '');

        /**
         * @var Model\Course $course
         */
        foreach ($courseTable->fetchByFilter() as $course) {
            $valueOptions[] = [
                'value' => $course->getId(),
                'label' => $course->getName()
            ];
        }

        $this->setValueOptions($valueOptions ?? []);
    }
}
