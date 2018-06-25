<?php

namespace Scorm\Action;

use Scorm\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class Delete
{
    /**
     * @var Model\ScormTable
     */
    private $scormTable;

    public function __construct(Model\ScormTable $scormTable)
    {
        $this->scormTable = $scormTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $this->scormTable->delete($id);
        return new JsonResponse(['status' => 200]);
    }
}