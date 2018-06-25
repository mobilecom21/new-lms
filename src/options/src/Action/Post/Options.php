<?php

namespace Options\Action\Post;

use Options\InputFilter;
use Options\Model\OptionsTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;

class Options
{
    /**
     * @var OptionsTable
     */
    private $optionsTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(OptionsTable $optionsTable, UrlHelper $urlHelper)
    {
        $this->optionsTable = $optionsTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $filter = new InputFilter\Options();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $values = $filter->getValues();
        if (isset($values['amazon_secret']) && empty($values['amazon_secret'])) {
            unset($values['amazon_secret']);
        }

        if (isset($values['stripe_secret_key']) && empty($values['stripe_secret_key'])) {
            unset($values['stripe_secret_key']);
        }

        $this->optionsTable->save($values);

        return new JsonResponse(['successMessage' => 'Options updated.']);
    }
}
