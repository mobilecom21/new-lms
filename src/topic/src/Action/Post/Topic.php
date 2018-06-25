<?php

namespace Topic\Action\Post;

use Topic\InputFilter;
use Topic\Model\TopicTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Topic
{
    /**
     * @var TopicTable
     */
    private $topicTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(TopicTable $topicTable, UrlHelper $urlHelper)
    {
        $this->topicTable = $topicTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Topic();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $save = $this->topicTable->save($filter->getValues());
        return new JsonResponse([
            'redirectTo' => (($this->urlHelper)('course/view/single', ['id' => $data['parentId']]))
        ]);
    }
}
