<?php

namespace Student\View\Helper;

use Course;

class CourseUserTable
{
    /**
     * @var 
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
