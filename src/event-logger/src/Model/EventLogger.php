<?php

namespace EventLogger\Model;

class EventLogger
{
    /**
     * @var int
     */
    protected $id;

    

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Options
     */
    public function setId(int $id): Options
    {
        $this->id = $id;
        return $this;
    }

}
