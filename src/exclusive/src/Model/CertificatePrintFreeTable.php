<?php

namespace Exclusive\Model;

use Exclusive;
use Content;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Course\Model\CourseTableGateway;
use Course\Model\CourseUserTableGateway;

class CertificatePrintFreeTable
{
    /**
     * @var MessageTutorTableGateway
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

    public function __construct(CertificatePrintFreeTableGateway $tableGateway, ContentTableGateway $contenttableGateway, CourseTableGateway $coursetableGateway, CourseUserTableGateway $courseusertableGateway)
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

	public function isStudentFreePrintCertificateThisCourse(int $course_id, int $student_id) {
		$select = $this->tableGateway->getSql()->select()->where([
						'student_id = ?' => $student_id,
						'course_id = ?' => $course_id,
					]);
		$count = count($this->tableGateway->selectWith($select));

		if ($count > 0) { 
			return true; 
		} else {
			return false;
		}
	}

    public function save(int $userId, int ...$courseIds)
    {
        $this->tableGateway->delete(['student_id' => $userId]);

        foreach ($courseIds as $courseId) {
            $this->insert($courseId, $userId);
        }

        return count($courseIds);
    }

    protected function insert(int $courseId, int $userId): int
    {
        $this->tableGateway->insert([
            'course_id' => $courseId,
            'student_id' => $userId
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
}