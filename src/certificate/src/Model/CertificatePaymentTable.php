<?php

namespace Certificate\Model;

use Certificate;
use Content;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Course\Model\CourseTableGateway;
use Course\Model\CourseUserTableGateway;

class CertificatePaymentTable
{
    /**
     * @var PaymentTableGateway
     */
    private $tableGateway;

    /**
     * @var ContentTableGateway
     */
    private $contenttableGateway;

    /**
     * @var ContentTableGateway
     */
    private $coursetableGateway;

    /**
     * @var ContentTableGateway
     */
    private $courseusertableGateway;

    public function __construct(CertificatePaymentTableGateway $tableGateway, ContentTableGateway $contenttableGateway, CourseTableGateway $coursetableGateway, CourseUserTableGateway $courseusertableGateway)
    {
        $this->tableGateway = $tableGateway;
		$this->contenttableGateway = $contenttableGateway;
		$this->coursetableGateway = $coursetableGateway;
		$this->courseusertableGateway = $courseusertableGateway;
    }

    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function fetchByStudentIdExamId($studentId,$examId): ?ResultSet
    {
        if (!$studentId or !$examId) {
            return null;
        }

        return $this->tableGateway->select(['user_id' => $studentId, 'exam_id' => $examId]);
    }

    public function save($data)
    {		
		$this->insert($data);
    }

    protected function insert($data)
    {
        $this->tableGateway->insert([
			'user_id' => $data['student_id'],
            'address_id' => $data['address_id'],
            'exam_id' => $data['exam_id'],
			'amount' => $data['amount'],
        ]);

        return $this->tableGateway->lastInsertValue;
    }

    public function delete(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
    }

	public function fetchAll()
    {
        return $this->tableGateway->select();
    }

	public function fetchAllToArray()
    {
        return $this->tableGateway->select()->toArray();
    }

	public function fetchAllOrderByIdDesc()
    {
        return $this->tableGateway->select(function (Select $select) {$select->order('id DESC');});
    }
}