<?php

namespace Assignment\Action;

use Assignment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class Unlock
{
    /**
     * @var Model\AssignmentTable
     */
    private $assignmentTable;

    public function __construct(Model\AssignmentTable $assignmentTable)
    {
        $this->assignmentTable = $assignmentTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $this->assignmentTable->unlock($id);
        return new JsonResponse(['status' => 200]);
    }
}
