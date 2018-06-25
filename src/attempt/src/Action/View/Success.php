<?php

namespace Attempt\Action\View;

use Attempt\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use User\Model\UserMetaTable;
use Exam\Model\ExamTable;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Session\Container;
use Zend\Diactoros\Response\RedirectResponse;
use Certificate\Model\CertificatePaymentTable;
use Certificate\Model\CertificateAddressTable;
use User\Model\User;
use Exclusive\Model\CertificatePrintFreeTable;

class Success
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\AttemptTable
     */
    private $attemptTable;

    /**
     * @var Model\UserMetaTable
     */
    private $usermetaTable;

    /**
     * @var Model\ExamTable
     */
    private $examTable;

    /**
     * @var CertificatePaymentTable;
     */
    private $certificatePaymentTable;

    /**
     * @var CertificateAddressTable
     */
    private $certificateAddressTable;

    /**
     * @var Model\CertificatePrintFreeTable
     */
    private $certificatePrintFreeTable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(Template\TemplateRendererInterface $template, Model\AttemptTable $attemptTable, UserMetaTable $usermetaTable,	ExamTable $examTable, CertificatePaymentTable $certificatePaymentTable, CertificateAddressTable $certificateAddressTable, CertificatePrintFreeTable $certificatePrintFreeTable, UrlHelper $urlHelper)
    {
        $this->template = $template;
        $this->attemptTable = $attemptTable;
        $this->usermetaTable = $usermetaTable;
        $this->examTable = $examTable;
        $this->certificatePaymentTable = $certificatePaymentTable;
        $this->certificateAddressTable = $certificateAddressTable;
        $this->certificatePrintFreeTable = $certificatePrintFreeTable;
        $this->urlHelper = $urlHelper;

    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        $length = 12;
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $coupon = "";

        for ($i = 0; $i < $length; $i++) {
            $coupon .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        $session = new Container('offers15');
        if($session->offsetExists('coupon')) {
            $session->offsetUnset('coupon');
            $session->offsetSet('coupon', $coupon);
            //$session->setExpirationSeconds('600');
        } else {
            $session->offsetSet('coupon', $coupon);
            //$session->setExpirationSeconds('600');
        }

        $id = $request->getAttribute('id');
        $attempts = $this->attemptTable->fetchById($id)->current();
        if($attempts) {
            $score = $attempts->getScore();
            $examId = $attempts->getExamId();
            $courseId = $this->examTable->GetCourseIdfromExamId($examId);
        } else {
            $score = 0;
            $examId = NULL;
            $courseId = NULL;
            //return new RedirectResponse(($this->urlHelper)('home'));
        }

        $currentUser = $request->getAttribute(User::class);
        $currentUserId = $currentUser->getId();
        $certificatepayment = $this->certificatePaymentTable->fetchByStudentIdExamId($currentUserId,$examId);
        $isCertificatePaymentExist = false;
        if($certificatepayment) {
            $countpayment = count($certificatepayment);
            if ($countpayment > 0) {
                $isCertificatePaymentExist = true;
            }
        }

        $isFreePrntCert = false;
        $isFreePrntCert = $this->certificatePrintFreeTable->isStudentFreePrintCertificateThisCourse($courseId,$currentUserId);

        if($isFreePrntCert && !$isCertificatePaymentExist) {
            $data['student_id'] = $currentUserId;
            $data['exam_id'] = $examId;
            $data['amount'] = 0;

            $address = $this->certificateAddressTable->fetchByUserId($currentUserId)->current();
            $addressId = null;

            if (count($address) > 0) {
                $addressId = $address->getId();
            } else {
                $freeprintcoupon = 'freeprint_'.$id;
                return new RedirectResponse(($this->urlHelper)('certificate/form/address',['examid' => $examId,'coupon' => $freeprintcoupon,'id' => null]));
            }
            $data['address_id'] = $addressId;

            $save = $this->certificatePaymentTable->save($data);
        }

        return new HtmlResponse($this->template->render('attempt::success', [
            'resultSet' => $this->attemptTable->fetchbyId($id),
            'score' => $score,
            'certificate' => $this->urlHelper->generate('student/attempt/view/certificate', ['id' => $id]),
            'coupon' => $coupon,
            'attempts' => $attempts,
            'usermetaTable' => $this->usermetaTable,
            'isCertificatePaymentExist' => $isCertificatePaymentExist,
            'isFreePrintCertificate' => $isFreePrntCert,
        ]));
    }
}
