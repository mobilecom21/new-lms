<?php

namespace Certificate\Action\Json;

use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class State
{
    /**
     * @var Model\StateTable
     */
    private $stateTable;

    public function __construct(Model\StateTable $stateTable)
    {
		$this->stateTable = $stateTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parsedBody = $request->getParsedBody();

        $countryId = $parsedBody['id'] ?? null;

		$result = array();
		if ($countryId) {
			$result = $this->stateTable->fetchByCountryId($countryId)->toArray();
		}
		
		$options = '<option value="">Please select State</option>';
		foreach ($result as $state) 
		{
			$options .= '<option value="'.$state['id'].'">'.$state['name'].'</option>';
			//$states[$state['id']] = $state['name'];
		}
	
		//$list = array();
		
        return new JsonResponse($options);
    }
}
