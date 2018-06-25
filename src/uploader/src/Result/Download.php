<?php

namespace Uploader\Result;


class Download
{
    /**
     * @var array
     */
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}