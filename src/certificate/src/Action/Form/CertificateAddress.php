<?php

namespace Certificate\Action\Form;

use Certificate\Form;
use Certificate\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use User;
use Course;
use Exam;

class CertificateAddress
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\AddressTable
     */
    private $addressTable;

    /**
     * @var Model\StateTable
     */
    private $stateTable;

    /**
     * @var Model\CountryTable
     */
	public $countryTable;

    /**
     * @var ExamTableGateway
     */
    private $examtriesTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\CertificateAddressTable $addressTable, Model\StateTable $stateTable, Model\CountryTable $countryTable, Course\Model\CourseTable $courseTable, Exam\Model\ExamTriesTable $examtriesTable)
    {
        $this->template = $template;
        $this->addressTable = $addressTable;
		$this->stateTable = $stateTable;
		$this->countryTable = $countryTable;
		$this->courseTable = $courseTable;
		$this->examtriesTable = $examtriesTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody();
		$examId = $request->getAttribute('examid');
		$addressId = $request->getAttribute('id');
		$coupon = $request->getAttribute('coupon');

        $currentUser = $request->getAttribute(User\Model\User::class);
		$currentUserId = (int) $currentUser->getId();

        $form = new Form\CertificateAddress();

        if ($addressId) {
            /**
             * @var Model\Exam $exam
             */
            $address = $this->addressTable->fetchById($addressId)->current();
        } else {
			$address = $this->addressTable->fetchByUserId($currentUserId)->current();	
			if (count($address) > 0) {
				$addressId = $address->getId();					
			} 
		}

        // bind object when id passed in url
        if (isset($address) && $address instanceof Model\CertificateAddress) {
            $form->bind($address);
        }

		$states = $this->stateTable->fetchAllToArray();
		$countries = $this->countryTable->fetchAllToArray();

		if($examId) {
			$courseId = $this->examtriesTable->GetCourseIdfromExamId($examId);
			$course = $this->courseTable->fetchById($courseId);
			if($course) {	
				$courseName = $course->getName();
			}
		}

        // populate adminId
        $form->setData(['user_id' => $currentUserId]);

        // populate parentId
        $form->setData(['exam_id' => $examId]);
		$form->setData(['id' => $addressId]);
		$form->setData(['coupon' => $coupon]);

		$data['notice'] = '';
		if(strpos($coupon, 'freeprint') !== false) {
			$data['notice'] = 'Please insert address where to send Printed Certificate for free.';
		}

        return new HtmlResponse($this->template->render('certificate::addressform', [
            'form' => $form,
			'examId' => $examId,
			'states' => $states,
			'countries' => $countries,
			'data' => $data
        ]));
    }
}
