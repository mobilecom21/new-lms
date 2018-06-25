<?php

namespace Exam\Action\Post;

use Exam\InputFilter;
use Exam\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Exam
{
    /**
     * @var Model\ExamTable
     */
    private $examTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Model\ExamTable $examTable, UrlHelper $urlHelper)
    {
        $this->examTable = $examTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Exam();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $save = $this->examTable->save($filter->getValues());

        return new JsonResponse([
            'redirectTo' => ($this->urlHelper)('course/view/single', ['id' => $data['courseId']])
        ]);
    }
}
