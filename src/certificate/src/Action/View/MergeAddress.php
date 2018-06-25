<?php

namespace Certificate\Action\View;

use Certificate\Model;
use User\Model\UserTable;
use Attempt;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Diactoros\Response\RedirectResponse;

class MergeAddress
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CertificateAddressTable
     */
    private $certificateAddressTable;

    /**
     * @var Model\userTable
     */
    private $userTable;

    /**
     * @var AttemptTable;
     */
    private $attemptTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Model\StateTable
     */
    private $stateTable;

    /**
     * @var Model\CountryTable
     */
	public $countryTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\CertificateAddressTable $certificateAddressTable, UserTable $userTable, Attempt\Model\AttemptTable $attemptTable, UrlHelper $urlHelper,  Model\StateTable $stateTable, Model\CountryTable $countryTable)
    {
        $this->template = $template;
		$this->certificateAddressTable = $certificateAddressTable;
		$this->userTable = $userTable;
		$this->attemptTable = $attemptTable;
		$this->urlHelper = $urlHelper;
		$this->stateTable = $stateTable;
		$this->countryTable = $countryTable;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
		$data = $request->getParsedBody();

		$ids = $data['id'];	
		foreach($ids as $key => $dataId) {
			$inputId = explode("|",$dataId);
			$attemptId = $inputId[0];
			$paymentId = $inputId[1];
			$new_ids[$key] = $attemptId;
		}

		$n = 0;
		$new_ids =  array_values(array_unique($new_ids));
		$count = count($new_ids);

		foreach($new_ids as $id) {			
			$attempts = $this->attemptTable->fetchById($id)->current();
			if($attempts) { 
				$student_id = $attempts->getStudentId(); 
				$exam_id = $attempts->getExamId(); 
			} else { 
				$student_id = NULL; 
				$exam_id = NULL; 
			}
			$fetch_address = $this->certificateAddressTable->fetchByUserId($student_id)->current();

			$user = $this->userTable->oneById($student_id);
			$name = $user->getFirstName()." ".$user->getLastName();

			$state = $this->stateTable->fetchById($fetch_address->getStateId())->current();
			$statename = $state->getName();

			$country =  $this->countryTable->fetchById($fetch_address->getCountryId())->current();
			$countryname = $country->getName();

			$address[$n] = $name."\n".wordwrap($fetch_address->getAddress(), 20)."\n".wordwrap($fetch_address->getAddress2(), 20)."\n".wordwrap($fetch_address->getCity(), 20)."\n".$statename ."\n".$countryname." ".$fetch_address->getPostalCode();
			$n++;
		}

		$docx = new \Certificate\Model\DOCXTemplate($_SERVER['DOCUMENT_ROOT'].'/AddressTemplate.docx');
		for($n=1;$n<=100;$n++) {
			if($n<=$count) {
				$k = $n - 1;
				$docx->set('address'.$n, $address[$k]);
			} else {
				$docx->set('address'.$n, '');
			}
		}

		$docx->saveAs('PrintAddress.docx');

		return new HtmlResponse(
		        file_get_contents('PrintAddress.docx'),
                '200',
                [
                    'Content-type' => 'application/msword',
                    'Content-Disposition' => 'attachment'. '; filename=PrintAddress.docx',
                ]
		);

    }
}
