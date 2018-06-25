<?php
namespace Uploader\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Uploader\Uploader;

class Delete
{
    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getParsedBody()['id'] ?? 0;
        if ($id > 0) {
            $this->uploader->delete($id);
        }
        return new JsonResponse([
            'id' => $id
        ]);
    }
}
