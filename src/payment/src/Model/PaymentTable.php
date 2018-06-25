<?php

namespace Payment\Model;

use Payment;
use Content;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Course\Model\CourseTableGateway;
use Course\Model\CourseUserTableGateway;

class PaymentTable
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

    public function __construct(PaymentTableGateway $tableGateway, ContentTableGateway $contenttableGateway, CourseTableGateway $coursetableGateway, CourseUserTableGateway $courseusertableGateway)
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

    public function save(int $userId, int $courseId)
    {
        //$this->tableGateway->delete(['course_id' => $courseId,'user_id' => $userId]);
        $this->insert($courseId, $userId);

    }

    protected function insert(int $courseId, int $userId): int
    {
        $this->tableGateway->insert([
            'course_id' => $courseId,
            'user_id' => $userId
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