<?php

namespace Exclusive\View\Helper;

use Exclusive;

class CertificatePrintFree
{
    /**
     * @var 
     */
    private $certificatePrintFreeTable;

    public function __construct(Exclusive\Model\CertificatePrintFreeTable $certificatePrintFreeTable)
    {
        $this->certificatePrintFreeTable = $certificatePrintFreeTable;
    }

    public function __invoke(): Exclusive\Model\CertificatePrintFreeTable
    {
        return $this->certificatePrintFreeTable;
    }
}
