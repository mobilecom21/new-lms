<?php

namespace Course\View\Helper;

use Course;

class CourseUser
{
    /**
     * @var Course\Model\CourseUserTable
     */
    private $courseUserTable;

    public function __construct(Course\Model\CourseUserTable $courseUserTable)
    {
        $this->courseUserTable = $courseUserTable;
    }

    public function __invoke(): Course\Model\CourseUserTable
    {
        return $this->courseUserTable;
    }
}
