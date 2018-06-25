<?php
namespace Uploader\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Template;
use Zend\Diactoros\Response\HtmlResponse;
use Uploader\Uploader;

class View
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
            $media = $this->uploader->get([$response['uploader']->getId()]);
            $file = $this->uploader->download($media[0]['path']);
            if (empty($file->getContent())) {
                return new HtmlResponse($this->template->render('error::404'), 404);
            }
            header("Content-length: ". $media[0]['size']);
            header("Content-type: ". $media[0]['type']);
            echo $file->getContent();
        }
        return new HtmlResponse($this->template->render('error::404'), 404);
    }
}
