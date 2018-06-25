<?php
namespace Uploader\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Uploader\Uploader;
use User;

class Upload
{
    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Uploader $uploader, UrlHelper $urlHelper)
    {
        $this->uploader = $uploader;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var User\Model\User $currentUser
         */
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();
        if (0 === strpos($request->getUploadedFiles()['file']->getClientFilename(), 'blobid')) {
            $this->uploader->initializeAllowedMediaTypes([
                'image/jpeg',
                'image/png',
                'image/bmp'
            ]);
            $id = $this->uploader->upload($request->getUploadedFiles()['file'], $currentUserId);
            $media = $this->uploader->get([$id]);
            $key = '';
            if (! empty($media[0]['path']) && ! empty($media[0]['size'])) {
                $key = base64_encode(serialize([$id, $media[0]['path'], $media[0]['size']]));
            }
            return new JsonResponse([
                'location' => $this->urlHelper->generate('uploader/view', ['key' => $key])
            ]);
        }
        $this->uploader->initializeAllowedMediaTypes([
            'image/jpeg',
            'image/png',
            'image/bmp',

            'text/plain',
            'text/csv',
            'text/tab-separated-values',
            'text/calendar',
            'text/richtext',
            'text/html',

            'application/zip',
            'application/pdf',
            'application/rtf',

            'application/msword',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-write',
            'application/vnd.ms-excel',
            'application/vnd.ms-access',
            'application/vnd.ms-project',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.ms-word.template.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'application/vnd.ms-powerpoint.slide.macroEnabled.12',

            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.chart',
            'application/vnd.oasis.opendocument.database',
            'application/vnd.oasis.opendocument.formula',

            'application/vnd.apple.keynote',
            'application/vnd.apple.numbers',
            'application/vnd.apple.pages',
        ]);
        return new JsonResponse([
            'id' => $this->uploader->upload($request->getUploadedFiles()['file'], $currentUserId)
        ]);
    }
}
