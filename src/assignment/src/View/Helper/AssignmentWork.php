<?php

namespace Assignment\View\Helper;

use Assignment;

class AssignmentWork
{
    /**
     * @var Assignment\Model\AssignmentWorkTable
     */
    private $assignmentWorkTable;

    public function __construct(Assignment\Model\AssignmentWorkTable $assignmentWorkTable)
    {
        $this->assignmentWorkTable = $assignmentWorkTable;
    }

    public function __invoke(): Assignment\Model\AssignmentWorkTable
    {
        return $this->assignmentWorkTable;
    }
}
