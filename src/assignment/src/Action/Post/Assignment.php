<?php

namespace Assignment\Action\Post;

use Assignment\InputFilter;
use Assignment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Assignment
{
    /**
     * @var Model\AssignmentTable
     */
    private $assignmentTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\AssignmentTable $assignmentTable, UrlHelper $urlHelper)
    {
        $this->assignmentTable = $assignmentTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];
        $filter = new InputFilter\Assignment();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $save = $this->assignmentTable->save($filter->getValues());

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('course/view/single', ['id' => $data['courseId']])
        ]);
    }
}
