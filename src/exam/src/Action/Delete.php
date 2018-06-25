<?php

namespace Exam\Action;

use Exam\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class Delete
{
    /**
     * @var Model\ExamTable
     */
    private $examTable;

    public function __construct(Model\ExamTable $examTable)
    {
        $this->examTable = $examTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $this->examTable->delete($id);
        return new JsonResponse(['status' => 200]);
    }
}