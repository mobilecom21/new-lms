<?php

namespace Exclusive\Action\Json;

use Exclusive\Model;
use User;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Exclusive\Model\MessageTutorTable;

class CertificatePrintFree
{
    /**
     * @var Model\CourseUserTable
     */
    private $certificatePrintFreeTable;

    public function __construct(Model\CertificatePrintFreeTable $certificatePrintFreeTable)
    {
        $this->certificatePrintFreeTable = $certificatePrintFreeTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parsedBody = $request->getParsedBody();

        $courseId = $parsedBody['courseId'] ?? null;
        $studentId = $parsedBody['studentId'] ?? null;
		$name = $parsedBody['name'] ?? null;
		$name = str_replace("[course]","",$name);

		$isStudentFreePrintCertificateThisCourse = false;
		
		if ($studentId) {
			$isStudentFreePrintCertificateThisCourse = $this->certificatePrintFreeTable->isStudentFreePrintCertificateThisCourse($courseId,$studentId);
		}
		
		if($isStudentFreePrintCertificateThisCourse) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
        
		$form = '<div class="checkbox-toggle mt-2 mb-3"><input name="'.$name.'[certificateprintfree][]" type="checkbox" id="certificateprintfree-toggle-'.$courseId.'" value="'.$courseId.'" '.$checked.'><label for="certificateprintfree-toggle-'.$courseId.'">Free Print Certificate</label></div>';


		$list['htmloutput'] = $form;

		//$list = array();
		
        return new JsonResponse($list);
    }
}
