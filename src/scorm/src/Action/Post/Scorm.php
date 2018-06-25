<?php

namespace Scorm\Action\Post;

use Scorm\InputFilter;
use Scorm\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Scorm
{
    /**
     * @var Model\ScormTable
     */
    private $scormTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\ScormTable $scormTable, UrlHelper $urlHelper)
    {
        $this->scormTable = $scormTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Scorm();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $save = $this->scormTable->save($filter->getValues());
        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('course/view/single', ['id' => $data['courseId']])
        ]);
    }
}
