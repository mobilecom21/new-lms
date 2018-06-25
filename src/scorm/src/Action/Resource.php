<?php

namespace Scorm\Action;

use Scorm\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\HtmlResponse;

class Resource
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
        $file = 'data/media/scorm/' . $request->getAttribute('path');
        if ( ! file_exists($file)) {
            return new HtmlResponse(
                'Not Found',
                '404'
            );
        }
        $mime_type = mime_content_type($file);
        if (strpos($file, '.css')) {
            $mime_type = 'text/css; charset=utf-8';
        }
        if (! $mime_type || 'document/unknown' == $mime_type) {
            $mime_type = 'application/octet-stream';
        }
        if ('text/plain' == $mime_type) {
            $mime_type = 'text/plain; charset=utf-8';
        }
        $filesize = filesize($file);
        if ($filesize <= 2147483647) {
            $content = file_get_contents($file);
        } else {
            $content = 'larger, to do handle larger files';
        }
        $content = str_replace('</head>', '</head><script type="text/javascript" language="javascript" src="/dist/startui/scripts/scorm/scorm_api.js"></script>', $content);
        return new HtmlResponse(
            $content,
            '200',
            [
                'Content-Type' => $mime_type,
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, must-revalidate, pre-check=0, post-check=0, max-age=0, no-transform',
                'Expires' => gmdate('D, d M Y H:i:s', 0) .' GMT',
                'Pragma' => 'no-cache',
                'Access-Control-Allow-Origin' => '*',
                'Content-Length' => $filesize,
            ]
        );
    }
}