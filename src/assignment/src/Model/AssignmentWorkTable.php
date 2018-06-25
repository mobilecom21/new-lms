<?php
namespace Assignment\Model;
use Course\Model\Content;
use Course\Model\ContentTable;
use Course\Model\CourseTable;
use Topic\Model\Attachment;
use Topic\Model\AttachmentTable;
use Topic\Model\TopicTable;
use Tutor\Model\TutorStudentCourse;
use Tutor\Model\TutorStudentCourseTable;
use Uploader\Model\UploaderTable;
use User\Model\UserTable;
use User\Model\UserMetaTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
class AssignmentWorkTable
{
    /**
     * @var AssignmentWorkTableGateway
     */
    private $assignmentWorkTableGateway;
    /**
     * @var AssignmentTable
     */
    private $assignmentTable;
    /**
     * @var UserTable
     */
    private $userTable;
    /**
     * @var UserMetaTable
     */
    private $userMetaTable;
    /**
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;
    /**
     * @var UploaderTable
     */
    private $uploaderTable;
    /**
     * @var CourseTable
     */
    private $courseTable;
    /**
     * @var ContentTable
     */
    private $contentTable;
    /**
     * @var TopicTable
     */
    private $topicTable;
    /**
     * @var AttachmentTable
     */
    private $attachmentTable;
    public function __construct(
        AssignmentWorkTableGateway $assignmentWorkTableGateway,
        AssignmentTable $assignmentTable,
        UserTable $userTable,
        UserMetaTable $userMetaTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        UploaderTable $uploaderTable,
        CourseTable $courseTable,
        ContentTable $contentTable,
        TopicTable $topicTable,
        AttachmentTable $attachmentTable
    )
    {
        $this->assignmentWorkTableGateway = $assignmentWorkTableGateway;
        $this->assignmentTable = $assignmentTable;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->uploaderTable = $uploaderTable;
        $this->courseTable = $courseTable;
        $this->contentTable = $contentTable;
        $this->topicTable = $topicTable;
        $this->attachmentTable = $attachmentTable;
    }
    public function fetchById(int $id)
    {
        return $this->assignmentWorkTableGateway->select(['id' => $id]);
    }
    public function byWorkerAndAssignment(int $worker, int $assignment)
    {
        $select = $this->select()
            ->where([
                'worker = ?' => $worker,
                'assignment = ?' => $assignment
            ])
            ->order('id DESC');
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return $this->buildAssignmentWork($resultSet);
    }
    public function byWorkerAndCourse(int $worker, int $course)
    {
        /*
        $select = $this->select()
            ->where([
                'student_id = ?' => $worker,
                'course_id = ?' => $course
            ])
            ->order('id DESC');
            
        $resultSet = $this->tutorStudentCourseTable->fetchByStudentAndCourse($worker, $course);
        return $this->buildAssignmentWork($resultSet);
*/
        $statement = $this->assignmentWorkTableGateway->getAdapter()->query("
            SELECT * FROM assignment_work aw
            LEFT JOIN assignment a ON a.id=aw.assignment
            LEFT JOIN topic_attachment ta ON ta.attachment_id=aw.assignment
            LEFT JOIN topic t ON t.id=ta.topic_id
            LEFT JOIN course_content c ON c.content_id=t.id
            WHERE worker={$worker} AND course_id={$course} AND ta.attachment='Assignment'
        ");
        /*
            SELECT aw.id FROM assignment_work aw, course_content cc 
            WHERE aw.worker = '$worker'
                AND aw.assignment=cc.content_id AND cc.course_id = '{$course}'
            ORDER BY aw.id DESC LIMIT 1
            SELECT * FROM assignment_work aw
            LEFT JOIN assignment a ON a.id=aw.assignment
            LEFT JOIN topic_attachment ta ON ta.attachment_id=aw.assignment
            LEFT JOIN topic t ON t.id=ta.topic_id
            LEFT JOIN course_content c ON c.content_id=t.id
            WHERE worker=9 AND ta.attachment='Assignment'
        */
        /*
        SELECT * FROM assignment a LEFT JOIN topic_attachment t ON a.id=t.attachment_id LEFT JOIN course_content c ON c.content_id=t.topic_id WHERE a.id=739 AND t.attachment='Assignment'
        */
        $resultSet = $this->assignmentWorkTableGateway->getResultSetPrototype();
        $result = $resultSet->initialize($statement->execute());
        return $this->buildAssignmentWork($result);
    }
    public function hasCompleteByWorkerAndTutorAndAssignment(int $worker, int $tutor, int $assignment): bool
    {
        $where = new Where();
        $where->EqualTo('worker', $worker);
        $where->EqualTo('tutor', $tutor);
        $where->EqualTo('assignment', $assignment);
        $where->EqualTo('status', AssignmentWork::STATUS_PASS);
        $statement = $this->assignmentWorkTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->assignmentWorkTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $completeCollection = $statement->execute();
        if ($completeCollection->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasFailedByWorkerAndTutorAndAssignment(int $worker, int $tutor, int $assignment): bool
    {
        $where = new Where();
        $where->EqualTo('worker', $worker);
        $where->EqualTo('tutor', $tutor);
        $where->EqualTo('assignment', $assignment);
        $where->EqualTo('status', AssignmentWork::STATUS_FAIL);
        $statement = $this->assignmentWorkTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->assignmentWorkTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $optionCollection = $statement->execute();
        if ($optionCollection->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasCompleteByWorkerAndAssignment(int $worker, int $assignment): bool
    {
        $where = new Where();
        $where->EqualTo('worker', $worker);
        $where->EqualTo('assignment', $assignment);
        $where->EqualTo('status', AssignmentWork::STATUS_PASS);
        $statement = $this->assignmentWorkTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->assignmentWorkTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $completeCollection = $statement->execute();
        if ($completeCollection->count() > 0) {
            return true;
        }
        return false;
    }
    public function hasFailedByWorkerAndAssignment(int $worker, int $assignment): bool
    {
        $where = new Where();
        $where->EqualTo('worker', $worker);
        $where->EqualTo('assignment', $assignment);
        $where->EqualTo('status', AssignmentWork::STATUS_FAIL);
        $statement = $this->assignmentWorkTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->assignmentWorkTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $optionCollection = $statement->execute();
        if ($optionCollection->count() > 0) {
            return true;
        }
        return false;
    }
    public function byTutor(int $tutor)
    {
        $select = $this->select()
            ->order('days_since_create ASC')
            ->where([
                'tutor = ?' => $tutor
            ]);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return $this->buildAssignmentWork($resultSet);
    }
    public function countOverdueByTutor(int $tutor)
    {
        $select = $this->select()
            ->where(['status = ?' => AssignmentWork::STATUS_WAIT, 'tutor' => $tutor])
            ->having(['days_since_create > marking_days']);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return count($resultSet);
    }
    public function countUnmarkedByTutor(int $tutor)
    {
        $select = $this->select()
            ->where(['status = ?' => AssignmentWork::STATUS_WAIT, 'tutor' => $tutor]);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return count($resultSet);
    }
    public function byWorker(int $worker)
    {
        $select = $this->select()
            ->order('days_since_create ASC')
            ->where([
                'worker = ?' => $worker
            ]);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return $this->buildAssignmentWork($resultSet);
    }
    public function usingFilter(?string $filter)
    {
        $select = $this->select()
            ->order('days_since_create ASC');
        $this->selectFilter($filter, $select);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return $this->buildAssignmentWork($resultSet);
    }
    public function byTutorUsingFilter(int $tutor, ?string $filter)
    {
        $select = $this->select()
            ->order('days_since_create ASC')
            ->where([
                'tutor = ?' => $tutor
            ]);
        $this->selectFilter($filter, $select);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return $this->buildAssignmentWork($resultSet);
    }
    public function byWorkAndTutor(int $work, int $tutor)
    {
        $select = $this->select()
            ->where([
                'id' => $work,
                'tutor' => $tutor
            ]);
        $resultSet = $this->assignmentWorkTableGateway->selectWith($select);
        return $this->buildAssignmentWork($resultSet);
    }
    public function byWorkerAndTutor(int $worker, int $tutor)
    {
        return $this->assignmentWorkTableGateway->select([
            'worker' => $worker,
            'tutor' => $tutor
        ])->toArray();
    }
    public function fetchLastWorkByWorkerAndAssignment(int $worker, int $assignment)
    {
        $statement = $this->assignmentWorkTableGateway->getAdapter()->query("
            SELECT aw.id FROM assignment_work aw WHERE aw.worker = '$worker' AND aw.assignment = '$assignment' ORDER BY aw.id DESC LIMIT 1
        ");
        $resultSet = $this->assignmentWorkTableGateway->getResultSetPrototype();
        $lastWork = $resultSet->initialize($statement->execute())->toArray();
        return $lastWork[0]['id'] ?? 0;
    }
    public function updateTutor($id, $tutor)
    {
        return $this->assignmentWorkTableGateway->update([
            'tutor' => $tutor
        ], [
            'id' => $id,
        ]);
    }
    public function viewed($work, $tutor)
    {
        return $this->assignmentWorkTableGateway->update([
            'viewed' => 1
        ], [
            'id' => $work,
            'tutor' => $tutor
        ]);
    }
    public function countUnread($tutor)
    {
        return count($this->assignmentWorkTableGateway->select(['tutor' => $tutor, 'viewed' => 0]));
    }
    public function markWork(int $tutor, int $work, int $feedbackUploader, int $status)
    {
        return $this->assignmentWorkTableGateway->update([
            'feedback_uploader' => $feedbackUploader,
            'status' => $status,
            'updated_at' => time(),
        ], [
            'id' => $work,
            'tutor' => $tutor
        ]);
    }
    public function submitWork(int $worker, int $assignment, int $assignmentUploader)
    {
        /**
         * @var Attachment $attachment
         * @var Content $course
         * @var TutorStudentCourse $tutorStudentCourse
         */
        $attachment = $this->attachmentTable->oneAssignmentById($assignment);
        $topicId = $attachment->getTopicId();
        $course = $this->contentTable->oneByContentId($topicId);
        $courseId = $course->getCourseId();
        $tutorStudentCourse = $this->tutorStudentCourseTable->fetchTutorForStudentAndCourse($worker, $courseId);
        $this->assignmentWorkTableGateway->insert([
            'assignment' => $assignment,
            'worker' => $worker,
            'tutor' => $tutorStudentCourse->getTutorId(),
            'assignment_uploader' => $assignmentUploader,
            'status' => AssignmentWork::STATUS_WAIT,
            'created_at' => time()
        ]);
        return [1];
    }
    private function selectFilter(?string $filter, Select $select)
    {
        switch ($filter) {
            case 'marked':
                $select->where(['status > ?' => AssignmentWork::STATUS_WAIT]);
                break;
            case 'passed':
                $select->where(['status = ?' => AssignmentWork::STATUS_PASS]);
                break;
            case 'failed':
                $select->where(['status = ?' => AssignmentWork::STATUS_FAIL]);
                break;
            case 'due':
                $select->where(['status = ?' => AssignmentWork::STATUS_WAIT])
                ->having(['days_since_create <= marking_days']);
                break;
            case 'overdue':
                $select->where(['status = ?' => AssignmentWork::STATUS_WAIT])
                    ->having(['days_since_create > marking_days']);
                break;
            case 'overdue-marked':
                $select->where(['status != ?' => AssignmentWork::STATUS_WAIT])
                    ->having(['days_since_create - days_since_update > marking_days']);
                break;
        }
    }
    private function select()
    {
        return $this->assignmentWorkTableGateway->getSql()
            ->select()
            ->columns([
                '*',
                new Expression('(' . time() . ' - updated_at) / 86400 as days_since_update'),
                new Expression('(' . time() . ' - created_at) / 86400 as days_since_create'),
                new Expression("(SELECT user_meta.value FROM user_meta RIGHT JOIN assignment_work ON (user_meta.name = 'marking_days' AND user_meta.user_id = assignment_work.tutor) LIMIT 1) as marking_days")
            ]);
    }
    private function buildAssignmentWork(ResultSet $resultSet): array
    {
        foreach ($resultSet as $result) {
            /**
             * @var Attachment $attachment
             * @var Content $course
             */
            $attachment = $this->attachmentTable->oneAssignmentById($result->assignment);
            if (! $attachment) {
                continue;
            }
            $topicId = $attachment->getTopicId();
            unset($attachment);
            $course = $this->contentTable->oneByContentId($topicId);
            if (! $course) {
                continue;
            }
            $courseId = $course->getCourseId();
            unset($course);
            $feedbackUploader = $this->uploaderTable->fetchById($result->feedback_uploader)->current();
            $markingDays = 5;
            $markingRow = $this->userMetaTable->getMetaByName($result->tutor, 'marking_days')->current();
            if ($markingRow) {
                $markingDays = ! empty($markingRow->getValue()) ? (int) $markingRow->getValue() : 5;
            }
            if (AssignmentWork::STATUS_WAIT == $result->status) {
                //affects due and overdue
                $result->days_since_create = round($result->days_since_create);
                $start_date = date("Y-m-d", strtotime("now -{$result->days_since_create} days"));
                $markingDaysLeft = $this->marking_days_left($start_date, $markingDays);
               
                #$markingDaysLeft = $markingDays - $result->days_since_create;
            } else {
                //affects marked, passed, failed, overdue_marked
                $result->days_since_create = round($result->days_since_create);
                $result->days_since_update = round($result->days_since_update);
                $start_date = date("Y-m-d", strtotime("-" . ($result->days_since_create - $result->days_since_update) . " days"));
                $markingDaysLeft = $this->marking_days_left($start_date, $markingDays);
                #$markingDaysLeft = $markingDays - ($result->days_since_create - $result->days_since_update);
                
            }
            $results[] = new AssignmentWork(
                $result->id,
                $this->assignmentTable->fetchById($result->assignment),
                $this->userTable->oneById($result->worker),
                $this->userTable->oneById($result->tutor),
                $this->uploaderTable->fetchById($result->assignment_uploader)->current(),
                $feedbackUploader ? $feedbackUploader : null,
                $this->courseTable->fetchById($courseId),
                $this->topicTable->fetchById($topicId)->current(),
                $result->status,
                $result->created_at,
                $result->updated_at,
                $markingDaysLeft
            );
        }
        return $results ?? [];
    }
    private function marking_days_left($start_date, $marking_days_available, $recursive=false) : int {
    //public holidays snatched from http://www.calendarpedia.co.uk/bank-holidays/bank-holidays-2022.html
    $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
    $holidayDays = ['*-12-25','*-12-26','*-01-01',  //christmas, boxing day, new years day
                    '2018-05-07',   //early may bank holiday
                    '2018-05-28',   //spring bank holiday
                    '2018-08-27',   //august bank holiday
                    '2019-04-19',   //good friday
                    '2019-04-22',   //easter monday
                    '2019-05-06',   //early may bank holiday
                    '2019-05-27',   //spring bank holiday
                    '2019-08-26',   //august bank holiday,
                    '2020-04-10',   //good friday
                    '2020-04-13',   //easter monday
                    '2020-05-04',   //early may bank holiday
                    '2020-05-25',   //spring bank holiday
                    '2020-08-31',   //august bank holiday
                    '2020-12-28',   //substitue for boxing day
                    '2021-04-02',   //good friday
                    '2021-04-05',   //easter monday
                    '2021-05-03',   //early may bank holiday
                    '2021-05-31',   //spring bank holiday
                    '2021-08-30',   //august bank holiday
                    '2021-12-27',   //substitue for christmas day
                    '2021-12-28',   //substitue for boxing day
                    '2022-01-03',   //substitue for new year's day
                    '2022-04-15',   //good friday
                    '2022-04-18',   //easter monday
                    '2022-05-02',   //early may bank holiday
                    '2022-05-30',   //spring bank holiday
                    '2022-08-29',   //august bank holiday
                    '2022-12-27',   //substitue for christmas day
                    '2023-01-02',   //substitue for new year's day
                    '2023-04-07',   //good friday
                    '2023-04-10',   //easter monday
                    '2023-05-01',   //early may bank holiday
                    '2023-05-29',   //spring bank holiday
                    '2023-08-28'    //august bank holiday
                    ]; // variable and fixed holidays
    //start from the date supplied (will usually be the work submission date)
    $from = new \DateTime($start_date);
    //begin the end date from the start date and then add the number of marking days 
    $to = new \DateTime($start_date);
    $to->modify("+{$marking_days_available} days");
    //set the interval period to be one day
    $interval = new \DateInterval('P1D');
    $periods = new \DatePeriod($from, $interval, $to);
    //setup our vars for use later
    $weekenddays = 0;
    $setdays = 0;
    $regulardays = 0;
    //IT0R4TE!
    $days = 0;
    foreach ($periods as $period) {
        ##if you want to test this function independently
        ## remove the #comments for output that makes sense
        #echo $period->format("Y-m-d");
        
        if (!in_array($period->format('N'), $workingDays)) {
            //if the days are weekend days, add to the counter
            $weekenddays++;
            #echo " weekend";
        }
        if (in_array($period->format('Y-m-d'), $holidayDays)) {
             //if the days are fixed or recurring public holidays, add to the counter
            $setdays++;
            #echo " named holiday";
        }
        if (in_array($period->format('*-m-d'), $holidayDays)) {
            //if the days are fixed or recurring public holidays, add to the counter
            $regulardays++;
            #echo " regular holiday";
        }
        #echo "<br />";
        //normal day
        $days++;
        
        
    }
    //ok, we have our number of days. now we need to work out whether it's in fact elapsed.
    $adddays = $days + $weekenddays + $setdays + $regulardays;
    $today = new \DateTime("now");
    $from->modify("+{$adddays} days");
    //check if the start date plus the interval adjusted for working days is less than today
    if($from < $today) {
        //get the actual difference and return the days
        //format %r is a minus sign is applicable, %a is days
        $interval = $today->diff($from);
        return $interval->format("%r%a days");
    }
    
    //if we've not added enough working days because of awkward stuff like weekends and holidays, let's chuck them back into the bag and tot them up
    if(($weekenddays + $setdays + $regulardays) > 0) {
        $period->modify("+1 days");
        $days += $this->marking_days_left($period->format("Y-m-d"), ($weekenddays + $setdays + $regulardays), true);
    }
    //we're all good. hot diggidy. send some days
    //add one because we ignore the submission date itself
    // (but not if we're running this function recursively to catch up on funny dates)
    return $recursive ? $days : ++$days;
}
}