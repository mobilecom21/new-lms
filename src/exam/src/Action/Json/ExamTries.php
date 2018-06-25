<?php

namespace Exam\Action\Json;

use Exam\Model;
use User;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Exam\Model\ExamTable;
use Exam\Model\ExamTriesTable;

class ExamTries
{
    /**
     * @var Model\CourseUserTable
     */
    private $examTriesTable;

    /**
     * @var Model\CourseUserTable
     */
    private $examTable;

    public function __construct(Model\ExamTriesTable $examTriesTable, ExamTable $examTable)
    {
        $this->examTriesTable = $examTriesTable;
		$this->examTable = $examTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parsedBody = $request->getParsedBody();

        $courseId = $parsedBody['courseId'] ?? null;
        $studentId = $parsedBody['studentId'] ?? null;
		$name = $parsedBody['name'] ?? null;
		$name = str_replace("[course]","",$name);

		$isCourseNoLimit = false;
		
		if ($studentId) {
			$isCourseNoLimit = $this->examTriesTable->isCourseNoLimit($courseId,$studentId);
		}
		
		if($isCourseNoLimit) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
        
		$form = '<div class="checkbox-toggle mt-2 mb-3"><input name="'.$name.'[exam][]" type="checkbox" id="check-toggle-'.$courseId.'" value="'.$courseId.'" '.$checked.'><label for="check-toggle-'.$courseId.'">Unlimited Exam Attempt</label></div>';


		$list['htmloutput'] = $form;

		//$list = array();
		
        return new JsonResponse($list);
    }
}
