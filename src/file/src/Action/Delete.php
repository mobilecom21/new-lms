<?php

namespace File\Action;

use File\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class Delete
{
    /**
     * @var Model\FileTable
     */
    private $fileTable;

    public function __construct(Model\FileTable $fileTable)
    {
        $this->fileTable = $fileTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');
        $this->fileTable->delete($id);
        return new JsonResponse(['status' => 200]);
    }
}