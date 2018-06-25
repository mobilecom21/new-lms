<?php

namespace Course\Action\Post;

use Course\InputFilter;
use Course\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;

class Course
{
    /**
     * @var Model\CourseTable
     */
    private $courseTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\CourseTable $courseTable, UrlHelper $urlHelper)
    {
        $this->courseTable = $courseTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Course();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $courseId = $this->courseTable->save($filter->getValues());

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('course/view/single', ['id' => $courseId])
        ]);
    }
}
