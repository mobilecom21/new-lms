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
use Options;
use Exam;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Session\Container;

class CertificatePayment
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CertificatePaymentTable
     */
    private $paymentTable;

    /**
     * @var Model\CertificateAddressTable
     */
    private $addressTable;

    /**
     * @var Course\Model\Course
     */
	public $coursetable;

    /**
     * @var Options
     */
	public $optionsTable;

    /**
     * @var ExamTableGateway
     */
    private $examtriesTable;

    /**
     * @var Model\StateTable
     */
    private $stateTable;

    /**
     * @var Model\CountryTable
     */
	public $countryTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Model\ExamTable
     */
	public $examTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\CertificatePaymentTable $paymentTable, Model\CertificateAddressTable $addressTable, Course\Model\CourseTable $courseTable, Options\Model\OptionsTable $optionsTable, Exam\Model\ExamTriesTable $examtriesTable, Model\StateTable $stateTable, Model\CountryTable $countryTable, UrlHelper $urlHelper, Exam\Model\ExamTable $examTable)
    {
        $this->template = $template;
        $this->paymentTable = $paymentTable;
		$this->addressTable = $addressTable;
		$this->courseTable = $courseTable;
		$this->optionsTable = $optionsTable;
		$this->examtriesTable = $examtriesTable;
		$this->stateTable = $stateTable;
		$this->countryTable = $countryTable;
		$this->urlHelper = $urlHelper;
		$this->examTable = $examTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $examId = $request->getAttribute('id');
		$coupon = $request->getAttribute('coupon');
		$data = $request->getParsedBody();

		//$coupon = $data->coupon;

		$session = new Container('offers15');
		if($session->offsetExists('coupon')) {
			$session_coupon = $session->offsetGet('coupon');
		} else {
			$session_coupon = '';
		}
		$discount = false;
		if ($coupon != '' && $coupon == $session_coupon) { $discount = true; }

		$exam = $this->examTable->fetchById($examId)->current();
		if (count($exam) == 0) {
			return new RedirectResponse(($this->urlHelper)('home'));
		}

        $form = new Form\CertificatePayment();

        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();
        //$form->setData(['studentId' => $currentUserId]);

		$address = $this->addressTable->fetchByUserId($currentUserId)->current();	
		$addressId = null;

		if (count($address) > 0) {
			$addressId = $address->getId();			
		} else {
			return new RedirectResponse(($this->urlHelper)('certificate/form/address',['examid' => $examId,'coupon' => $coupon,'id' => null]));
		}

		$form->setData(['address_id' => $addressId]);
		if($discount) {
			$form->setData(['amount' => '15']);
		} else { 
			$form->setData(['amount' => '25']);
		}

		$courseId = $this->examtriesTable->GetCourseIdfromExamId($examId);
		$course = $this->courseTable->fetchById($courseId);
		$courseName = $course->getName();

		$fetch_states = $this->stateTable->fetchAllToArray();
		$states = array();
		foreach($fetch_states as $state) {
			$states[$state['id']] = $state['name'];
		}
		$fetch_countries = $this->countryTable->fetchAllToArray();
		$country = array();
		foreach($fetch_countries as $country) {
			$countries[$country['id']] = $country['name'];
		}

        // populate adminId
        $currentUser = $request->getAttribute(User\Model\User::class);
        $currentUserId = (int) $currentUser->getId();
        $form->setData(['student_id' => $currentUserId]);

        // populate parentId
        $form->setData(['exam_id' => $examId]);
		$form->setData(['coupon' => $coupon]);

		$options = $this->optionsTable->fetchAll()->toArray();
		foreach ($options as $option) {
			if ('stripe_publishable_key' == $option['name']) {
				$stripe_publishable_key = $option['value'];
			}
			if ('stripe_secret_key' == $option['name']) {
				$stripe_secret_key = $option['value'];
			}
		}

        return new HtmlResponse($this->template->render('certificate::form', [
            'form' => $form,
			'address' => $address,
			'courseName' => $courseName,
			'stripe_publishable_key' => $stripe_publishable_key,
			'stripe_secret_key' => $stripe_secret_key,
			'states' => $states,
			'countries' => $countries,
			'addressId' => $addressId,
			'examId' => $examId,
			'discount' => $discount,
			'coupon' => $coupon,
        ]));
    }
}
