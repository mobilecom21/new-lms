<?php

namespace Exclusive\Model;

use Exclusive;
use Content;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Course\Model\CourseTableGateway;
use Course\Model\CourseUserTableGateway;

class MessageTutorTable
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

    public function __construct(MessageTutorTableGateway $tableGateway, ContentTableGateway $contenttableGateway, CourseTableGateway $coursetableGateway, CourseUserTableGateway $courseusertableGateway)
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

	public function isStudentCanNotMessageThisCourse(int $course_id, int $student_id) {
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

	public function isAllCourseDisallowMessaging(int $student_id) {
		$isAll = true;
		$courseIds= array();
		$select = $this->tableGateway->getSql()->select()->where([
						'student_id = ?' => $student_id
					]);
		$resultSet = $this->tableGateway->selectWith($select);
		if(count($resultSet) > 0) {
			foreach ($resultSet as $row) {
				$courseIds[] = $row->getCourseId();
			}
		}

        $table = $this->courseusertableGateway->getTable();
        $join = $this->coursetableGateway->getTable();

        $select = (new Select($table))->where(['user_id' => $student_id]);
        $select->join($join, $table . '.course_id = ' . $join . '.id');

        $statement = $this->courseusertableGateway->getSql()
            ->prepareStatementForSqlObject($select);

        $resultSet = $this->coursetableGateway->getResultSetPrototype();
        
		$findCourseId =  $resultSet->initialize($statement->execute());

		//var_dump($findCourseId);
		if(count($findCourseId) > 0) {			
			foreach ($findCourseId as $course) {
				//var_dump($course);
				if(!in_array($course->getId(),$courseIds)) {
					$isAll = false;
				}
			}
		}
		return $isAll;
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