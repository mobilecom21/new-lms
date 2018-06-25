<?php

namespace File\Action\Post;

use File\InputFilter;
use File\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class File
{
    /**
     * @var Model\FileTable
     */
    private $fileTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\FileTable $fileTable, UrlHelper $urlHelper)
    {
        $this->fileTable = $fileTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\File();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $save = $this->fileTable->save($filter->getValues());

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('course/view/single', ['id' => $data['courseId']])
        ]);
    }
}
