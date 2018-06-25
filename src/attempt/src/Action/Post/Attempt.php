<?php

namespace Attempt\Action\Post;

use Attempt\InputFilter;
use Attempt\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Exam\Model\ExamTable;
use Uploader\Uploader;

class Attempt
{
    /**
     * @var Model\AttemptTable
     */
    private $attemptTable;

    /**
     * @var Model\ExamTable
     */
    private $examTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Model\AttemptTable $attemptTable, UrlHelper $urlHelper)
    {
        $this->attemptTable = $attemptTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];
		//$data['expected_answer'] = 'test';
		$data['expected_answer'] = $this->attemptTable->GetExpectedAnswer($data['parentId']);

		$a = $data['expected_answer'];
		$b = $data['answer'];
		$c = 0;
		foreach ($b as $key => $i) {
			if ($a[$key] == $i) $c++;
		}
	
		$score = ($c/count($a))*100;

		$data['score'] = $score;

        $filter = new InputFilter\Attempt();
        $filter->setData($data);

        if (!$filter->isValid()) {
            return new JsonResponse([
                'errors' => $filter->getMessages()
            ]);
        }

        $save = $this->attemptTable->save($filter->getValues());	

		if($score >= 75) {
			return new JsonResponse([
				'redirectTo' => ($this->urlHelper)('attempt/view/success', ['id' => $save])
			]);
		} else {
			return new JsonResponse([
                'redirectTo' => ($this->urlHelper)('student/exam/view/single', ['id' => $data['parentId']], 
				array('previd' => $save)).'#failed'
            ]);
		}
    }
}
