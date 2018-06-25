<?php

namespace Uploader\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uploader\Uploader;
use Zend\Diactoros;
use Zend\Expressive\Template;
use Zend\Diactoros\Response\HtmlResponse;

class Download
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Template\TemplateRendererInterface $template, Uploader $uploader)
    {
        $this->template = $template;
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $key = $request->getAttribute('key');
        $response = $this->uploader->byKey($key);
        if ($response) {
            return $this->downloadFile($response['uploader']->getPath(), $response['uploader']->getName());
        }
        return new HtmlResponse($this->template->render('error::404'), 404);
    }

    protected function downloadFile(string $file, $name)
    {
        $body = new Diactoros\Stream('php://temp', 'w+');
        $body->write($this->uploader->download($file)->getContent());
        return (new Diactoros\Response)
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader(
                'Content-Disposition',
                "attachment; filename=" . $name
            )
            ->withHeader('Content-Transfer-Encoding', 'Binary')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Pragma', 'public')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withBody($body)
            ->withHeader('Content-Length', "{$body->getSize()}");
    }
}
