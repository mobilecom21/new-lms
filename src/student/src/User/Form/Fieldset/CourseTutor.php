<?php

namespace Student\User\Form\Fieldset;

use Course;
use Rbac\Role;
use Tutor;
use User;
use Exam;
use Exclusive;
use Zend\Form\Element\Button;
use Zend\Form\Fieldset;

class CourseTutor extends Fieldset
{
    /**
     * @var Course\Model\CourseUserTable
     */
    private $courseUserTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var Tutor\Model\TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    public function __construct(
        Course\Form\Element\Select\Course $courseSelectElement,
        User\Form\Element\EndOfSupport $endOfSupportElement,
        User\Form\Element\HomeLearningNumber $homeLearningNumber,
        User\Form\Element\OrderNumber $orderNumber,
        Tutor\User\Form\Element\Select\Tutor $tutorSelectElement,
        Course\Model\CourseUserTable $courseUserTable,
        User\Model\UserMetaTable $userMetaTable,
        Tutor\Model\TutorStudentCourseTable $tutorStudentCourseTable,
        Exam\Form\Element\MultiCheckbox\ExamTries $examAttemptNoLimitMultiCheckboxElement,
        Exam\Model\ExamTriesTable $examTriesTable,
        Exam\Model\ExamTable $examTable,
        Exclusive\Form\Element\MultiCheckbox\MessageTutor $exclusivemessageTutorMultiCheckboxElement,
        Exclusive\Model\MessageTutorTable $messagetutorTable,
        Exclusive\Form\Element\MultiCheckbox\CertificatePrintFree $exclusivecertificatePrintFreeMultiCheckboxElement,
        Exclusive\Model\CertificatePrintFreeTable $certificatePrintFreeTable,
        $name = 'courseTutor',
        array $options = []
    )
    {
        parent::__construct($name, $options);
        $this->courseUserTable = $courseUserTable;
        $this->userMetaTable = $userMetaTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->examTriesTable = $examTriesTable;
        $this->messagetutorTable = $messagetutorTable;
        $this->certificatePrintFreeTable = $certificatePrintFreeTable;


        $courseSelectElement->removeAttribute('multiple');
        $courseSelectElement->setName('course');
        $courseSelectElement->setAttribute('onchange', 'onChangeCourseUpdateTutorSelect(this)');

        $this->add($courseSelectElement);
        $this->add($tutorSelectElement);

        $endOfSupportElement->setLabel('End of Support ');
        $this->add($endOfSupportElement);

        $homeLearningNumber->setLabel('HLN ');
        $this->add($homeLearningNumber);

        $orderNumber->setLabel('Order Nr ');
        $this->add($orderNumber);

        $removeButton = new Button('-');
        $removeButton->setLabel('-');
        $removeButton->setAttribute('onclick', 'formFieldsetRemoveIt(this)');
        $this->add($removeButton);

        $addButton = new Button('+');
        $addButton->setLabel('+');
        $addButton->setAttribute('onclick', 'formFieldsetDuplicateIt(this)');
        $addButton->setAttribute('class', 'lastbutton');
        $this->add($addButton);

        $examAttemptNoLimitMultiCheckboxElement->setName('exam');
        $this->add($examAttemptNoLimitMultiCheckboxElement);

        $exclusivemessageTutorMultiCheckboxElement->setName('messagetutor');
        $this->add($exclusivemessageTutorMultiCheckboxElement);

        $exclusivecertificatePrintFreeMultiCheckboxElement->setName('certificateprintfree');
        $this->add($exclusivecertificatePrintFreeMultiCheckboxElement);
    }

    public function populateValues($data)
    {
        foreach ($this->iterator as $name => $elementOrFieldset) {

            $request = $_SERVER["REQUEST_URI"];
            $requestArray = explode('/', $request);
            $studentId = (int) end($requestArray);

            /* @var Fieldset $elementOrFieldset */
            if (empty($data['course'])) {
                continue;
            }
            $currenCourseStudentExtra = $this->tutorStudentCourseTable->fetchByStudentAndTutorAndCourse($studentId, $data['tutor'], $data['course']);
            if ('end_of_support' == $name) {
                $endOfSupport = $currenCourseStudentExtra->getEndOfSupport();
                if ($endOfSupport > 0) {
                    $endTime = new \DateTime();
                    $endTime->setTimestamp($endOfSupport);
                    $timeStr = $endTime->format('Y-m-d');
                    $elementOrFieldset->setValue($timetr);

                    $data[$name] = $timeStr;

                }
            } elseif ('home_learning_number' == $name) {
                $elementOrFieldset->setValue($currenCourseStudentExtra->getHomeLearningNumber());
            }  elseif ('order_number' == $name) {
                $elementOrFieldset->setValue($currenCourseStudentExtra->getOrderNumber());
            } elseif ($elementOrFieldset instanceof Tutor\User\Form\Element\Select\Tutor) {
                foreach ($this->courseUserTable->fetchUserByCourseIdAndUserRole($data['course'], Role\Tutor::class) as $tutor) {
                    /* @var User\Model\User $tutor */
                    $tutorId = $tutor->getId();
                    $tutorMetas = $this->userMetaTable->fetchByUserId($tutorId)->toArray();
                    $first_name = $last_name = '';
                    foreach($tutorMetas as $tutorMeta) {
                        if ('first_name' == $tutorMeta['name']) {
                            $first_name = $tutorMeta['value'];
                        } elseif ('last_name' == $tutorMeta['name']) {
                            $last_name = $tutorMeta['value'];
                        }
                    }
                    $valueOptions[] = [
                        'value' => $tutorId,
                        'label' => $first_name . ' ' . $last_name
                    ];
                }
                $elementOrFieldset->setValueOptions($valueOptions ?? []);
            } elseif ($elementOrFieldset instanceof Exam\Form\Element\MultiCheckbox\ExamTries) {

                $isCourseNoLimit = $this->examTriesTable->isCourseNoLimit($data['course'],$studentId);

                if($isCourseNoLimit) {
                    $isnolimit = true;
                } else {
                    $isnolimit = false;
                }

                $examValueOptions[] = [
                    'value' => $data['course'],
                    'label' => 'Unlimited Exam Attempt',
                    'attributes' => [
                        'id' => 'check-toggle-'.$data['course']
                    ],
                    'label_attributes' => [
                        'for' => 'check-toggle-'.$data['course'],
                        'class' => 'examtriesInput'
                    ],
                    'selected' => $isnolimit,
                ];

                $elementOrFieldset->setValueOptions($examValueOptions ?? []);
            } elseif ($elementOrFieldset instanceof Exclusive\Form\Element\MultiCheckbox\MessageTutor) {

                $isStudentCanNotMessageThisCourse = $this->messagetutorTable->isStudentCanNotMessageThisCourse($data['course'],$studentId);

                if($isStudentCanNotMessageThisCourse) {
                    $isYes = true;
                } else {
                    $isYes = false;
                }

                $messageTutorValueOptions[] = [
                    'value' => $data['course'],
                    'label' => 'Disallow Tutor Messaging',
                    'attributes' => [
                        'id' => 'messagetutor-toggle-'.$data['course']
                    ],
                    'label_attributes' => [
                        'for' => 'messagetutor-toggle-'.$data['course'],
                        'class' => 'messagetutorInput'
                    ],
                    'selected' => $isYes,
                ];

                $elementOrFieldset->setValueOptions($messageTutorValueOptions ?? []);
            } elseif ($elementOrFieldset instanceof Exclusive\Form\Element\MultiCheckbox\CertificatePrintFree) {

                $isStudentFreePrintCertificateThisCourse = $this->certificatePrintFreeTable->isStudentFreePrintCertificateThisCourse($data['course'],$studentId);

                if($isStudentFreePrintCertificateThisCourse) {
                    $isYes = true;
                } else {
                    $isYes = false;
                }

                $certificatePrintFreeValueOptions[] = [
                    'value' => $data['course'],
                    'label' => 'Free Print Certificate',
                    'attributes' => [
                        'id' => 'certificateprintfree-toggle-'.$data['course']
                    ],
                    'label_attributes' => [
                        'for' => 'certificateprintfree-toggle-'.$data['course'],
                        'class' => 'certificateprintfreeInput'
                    ],
                    'selected' => $isYes,
                ];

                $elementOrFieldset->setValueOptions($certificatePrintFreeValueOptions ?? []);
            }
        }

        #throw new \Exception(var_dump($data));

        parent::populateValues($data);
    }
}
