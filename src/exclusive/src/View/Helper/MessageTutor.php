<?php

namespace Exclusive\View\Helper;

use Exclusive;

class MessageTutor
{
    /**
     * @var 
     */
    private $messageTutorTable;

    public function __construct(Exclusive\Model\MessageTutorTable $messageTutorTable)
    {
        $this->messageTutorTable = $messageTutorTable;
    }

    public function __invoke(): Exclusive\Model\MessageTutorTable
    {
        return $this->messageTutorTable;
    }
}
