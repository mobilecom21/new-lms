<?php
namespace Uploader\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Uploader\Uploader;

class Get
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
        $uploads = $request->getParsedBody()['uploads'] ?? '';
        $data = [];
        if (empty($uploads)) {
            return new JsonResponse([
                'data' => $data
            ]);
        }
        $uploads = json_decode($uploads, true);
        if (is_array($uploads) && count($uploads) > 0) {
            $data = $this->uploader->get($uploads);
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $v) {
                    unset($data[$k]['array_copy']);
                }
            }
        }
        return new JsonResponse($data);
    }
}
