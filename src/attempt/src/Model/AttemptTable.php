<?php

namespace Attempt\Model;

use Attempt\Model\AttemptTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Exam\Model\ExamTable;
use Uploader\Uploader;

class AttemptTable
{
    /**
     * @var AttemptTableGateway
     */
    private $tableGateway;

    /**
     * @var ExamTable
     */
    private $examTable;

    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(AttemptTableGateway $tableGateway, ExamTable $examTable, Uploader $uploader)
    {
        $this->tableGateway = $tableGateway;
		$this->examTable = $examTable;
		$this->uploader = $uploader;
    }

    public function countAttempt(int $student_id, int $exam_id)
    {
        $select = $this->tableGateway->getSql()->select()->where([
                'student_id = ?' => $student_id,
                'exam_id = ?' => $exam_id,
            ]);
        return count($this->tableGateway->selectWith($select));
    }

    public function countFailed(int $student_id, int $exam_id)
    {
        $select = $this->tableGateway->getSql()->select()->where([
                'student_id = ?' => $student_id,
                'exam_id = ?' => $exam_id,
				'(score < 75 or score IS NULL)'
            ]);
        return count($this->tableGateway->selectWith($select));
    }

    public function latestAttemptDateTime(int $student_id, int $exam_id)
    {
        $select = $this->tableGateway->getSql()->select()->where([
                'student_id = ?' => $student_id,
                'exam_id = ?' => $exam_id
            ]);
        $result = $this->tableGateway->selectWith($select);
		
		if (count($result) > 0) {
			
			foreach ($result as $row) {
				$datetime[] = $row->getCreatedAt();
			}
			usort($datetime, function($x, $y){
				$x = strtotime($x);
				$y = strtotime($y);
				if($x>$y){
					return 0; 
				}
				return 1;
			});

			$latestAttemptDateTime = $datetime[0];
		} elseif (count($result) == 1) {
			$latestAttemptDateTime = $result->current()->getCreatedAt();
		} else {
			$latestAttemptDateTime = null;
		}
		return $latestAttemptDateTime;
    }


    public function hasdownload($id)
    {
        return $this->tableGateway->update(['download' => 1], ['id' => $id]);
    }
    public function fetchById(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['id' => $ids]);
    }

    public function fetchByStudentId(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['student_id' => $ids]);
    }

    public function fetchByStudentIdExamId(int $studentid, int $examid): ?ResultSet
    {
        if (!$studentid) {
            return null;
        }
	
        if (!$examid) {
            return null;
        }

        return $this->tableGateway->select(['student_id' => $studentid,'exam_id' => $examid]);
    }


    public function save(array $data): int
    {

		$question_number = $data['question_number'];
        $this->tableGateway->insert([
				'exam_id' => $data['parentId'],
				'student_id' => $data['studentId'],
				'attempted_answer' => json_encode($data['answer']),
				'expected_answer' => json_encode($data['expected_answer']),
				'score' => $data['score']
        ]);

        $lastInsertValue = $this->tableGateway->lastInsertValue;

        // link file with topic
        //$this->topicAttachmentTable->add($data['parentId'], 'Exam', $lastInsertValue);

        return $lastInsertValue;
    }

	public function fetchAll()
    {
        return $this->tableGateway->select();
    }

	public function fetchAllOrderByIdDesc()
    {
        return $this->tableGateway->select(function (Select $select) {$select->order('id DESC');});
    }

	public function GetExpectedAnswer($parentId) {
		
		$exam = $this->examTable->fetchById($parentId)->current();
        $uploads = $exam->getUploads();
        $uploads = json_decode($uploads);
        $uploads = (Array)$uploads;
        $upload_id = reset($uploads);
        $upload = $this->uploader->get([$upload_id]);
        $upload = $upload[0] ?? [];
        if (! empty($upload['path']) && file_exists('data/media' . $upload['path'])) {
			$disposition = 'application/pdf' == $upload['type'] ? 'inline' : 'attachment';
			$file = 'data/media' . $upload['path'];
		}

		$row = 0;
		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				if ($row > 0) {
				$expected_answer[$row] = $data[$num - 1];
				}
				$row++;
			} 
			fclose($handle);
		}
		return $expected_answer;
	}
}