<?php

namespace Exclusive\Action\Json;

use Exclusive\Model;
use User;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Exclusive\Model\MessageTutorTable;

class MessageTutor
{
    /**
     * @var Model\CourseUserTable
     */
    private $messageTutorTable;

    public function __construct(Model\MessageTutorTable $messageTutorTable)
    {
        $this->messageTutorTable = $messageTutorTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parsedBody = $request->getParsedBody();

        $courseId = $parsedBody['courseId'] ?? null;
        $studentId = $parsedBody['studentId'] ?? null;
        $name = $parsedBody['name'] ?? null;
        $name = str_replace("[course]","",$name);

        $isStudentCanNotMessageThisCourse = false;

        if ($studentId) {
            $isStudentCanNotMessageThisCourse = $this->messageTutorTable->isStudentCanNotMessageThisCourse($courseId,$studentId);
        }

        if($isStudentCanNotMessageThisCourse) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        $form = '<div class="checkbox-toggle mt-2 mb-3"><input name="'.$name.'[messagetutor][]" type="checkbox" id="messagetutor-toggle-'.$courseId.'" value="'.$courseId.'" '.$checked.'><label for="messagetutor-toggle-'.$courseId.'">Disallow Tutor Messaging</label></div>';


        $list['htmloutput'] = $form;

        //$list = array();

        return new JsonResponse($list);
    }
}
