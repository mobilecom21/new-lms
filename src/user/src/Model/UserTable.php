<?php

namespace User\Model;

use Course\Model\CourseUserTable;
use Tutor\Model\TutorStudentCourseTable;
use Tutor\Model\TutorStudentCourseTableGateway;
use Exam\Model\ExamTriesTable;
use Exam\Model\ExamTriesTableGateway;
use Exclusive\Model\MessageTutorTable;
use Exclusive\Model\MessageTutorTableGateway;
use Exclusive\Model\CertificatePrintFreeTable;
use Exclusive\Model\CertificatePrintFreeTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class UserTable
{
    /**
     * @var UserTableGateway
     */
    private $userTableGateway;

    /**
     * @var UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var CourseUserTable
     */
    private $courseUserTable;

    /**
     * @var TutorStudentCourseTable
     */
    private $tutorStudentCourseTable;

    /**
     * @var TutorStudentCourseTableGateway
     */
    private $tutorStudentCourseTableGateway;

    /**
     * @var ExamTriesTable
     */
    private $examTriesTable;

    /**
     * @var ExamTriesTableGateway
     */
    private $examTriesTableGateway;

    /**
     * @var MessageTutorTable
     */
    private $messageTutorTable;

    /**
     * @var MessageTutorTableGateway
     */
    private $messageTutorTableGateway;

    /**
     * @var CertificatePrintFreeTable
     */
    private $certificatePrintFreeTable;

    /**
     * @var CertificatePrintFreeTableGateway
     */
    private $certificatePrintFreeTableGateway;

    public function __construct(UserTableGateway $userTableGateway, UserMetaTable $userMetaTable, CourseUserTable $courseUserTable, TutorStudentCourseTable $tutorStudentCourseTable, TutorStudentCourseTableGateway $tutorStudentCourseTableGateway, ExamTriesTable $examTriesTable, ExamTriesTableGateway $examTriesTableGateway, MessageTutorTable $messageTutorTable, MessageTutorTableGateway $messageTutorTableGateway, CertificatePrintFreeTable $certificatePrintFreeTable, CertificatePrintFreeTableGateway $certificatePrintFreeTableGateway)
    {
        $this->userTableGateway = $userTableGateway;
        $this->userMetaTable = $userMetaTable;
        $this->courseUserTable = $courseUserTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->tutorStudentCourseTableGateway = $tutorStudentCourseTableGateway;
        $this->examTriesTable = $examTriesTable;
        $this->examTriesTableGateway = $examTriesTableGateway;
        $this->messageTutorTable = $messageTutorTable;
        $this->messageTutorTableGateway = $messageTutorTableGateway;
        $this->certificatePrintFreeTable = $certificatePrintFreeTable;
        $this->certificatePrintFreeTableGateway = $certificatePrintFreeTableGateway;
    }

    /**
     * @param int $id
     *
     * @return array|\ArrayObject|User|null
     */
    public function oneById(int $id)
    {
        $resultSet = $this->userTableGateway->getResultSetPrototype();

        $select = (new Select($this->userTableGateway->getTable()))->where(['id' => $id]);
        $statement = $this->userTableGateway->getSql()->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result->count()) {
            return null;
        }
        $resultSet->initialize([
            [
                $result->current(),
                $this->userMetaTable->fetchByUserId($id),
                $this->courseUserTable->fetchByUserId($id),
                $this->tutorStudentCourseTable->fetchByStudentId($id)
            ]
        ]);

        return $resultSet->current();
    }

    public function byRole(string $role)
    {
        return $this->userTableGateway->select(['role' => $role]);
    }

    public function byIdentity(string $identity)
    {
        return $this->userTableGateway->select(['identity' => $identity]);
    }

    public function byToken(string $token)
    {
        $where = new Where();
        $where->equalTo('token', $token);
        $where->greaterThanOrEqualTo('token_time',time() - 7200);
        return $this->userTableGateway->select($where);
    }

    public function byUsername(string $username)
    {
        return $this->userTableGateway->select(['username' => $username]);
    }

    public function byRoleAndRbac(string $role, string $rbac, int $loggedId, array $pairs = [])
    {
        $ut = $this->userTableGateway->getTable();
        $tsct = $this->tutorStudentCourseTableGateway->getTable();
        $where = new Where();
        if ($role) {
            $where->equalTo($ut . '.role', $role);
        }
        foreach ($pairs as $identifier => $value) {
            $where
                ->nest()
                ->like($identifier, '%' . $value . '%')
                ->or
                ->like($this->userMetaTable->getTableName() . '.value', '%' . $value . '%')
                ->unnest();
        }
        $resultSet = $this->userTableGateway->getResultSetPrototype();
        if ('Rbac\Role\Tutor' == $rbac) {
            $where->equalTo($this->tutorStudentCourseTableGateway->getTable() . '.tutor_id', $loggedId);
            $statement = $this->userTableGateway->getSql()
                ->prepareStatementForSqlObject(
                    $this->userTableGateway->getSql()
                        ->select()
                        ->join(
                            $this->userMetaTable->getTableName(),
                            $ut . '.id = ' . $this->userMetaTable->getTableName() . '.user_id',
                            [],
                            Select::JOIN_LEFT
                        )
                        ->join($tsct, $tsct . '.student_id = ' . $ut . '.id', null, Select::JOIN_RIGHT)
                        ->where($where)
                        ->group($this->userTableGateway->getTable(). '.id')
                );
        } else {
            $statement = $this->userTableGateway->getSql()
                ->prepareStatementForSqlObject(
                    $this->userTableGateway->getSql()
                        ->select()
                        ->join(
                            $this->userMetaTable->getTableName(),
                            $this->userTableGateway->getTable() . '.id = ' . $this->userMetaTable->getTableName() . '.user_id',
                            [],
                            Select::JOIN_LEFT
                        )
                        ->where($where)
                        ->group($this->userTableGateway->getTable(). '.id')
                );
        }

        $userCollection = $statement->execute();
        foreach ($userCollection as $user) {
            $result[] = [
                $user,
                $this->userMetaTable->fetchByUserId($user['id']),
                $this->courseUserTable->fetchByUserId($user['id']),
                $this->tutorStudentCourseTable->fetchByStudentId($user['id'])
            ];
        }

        $resultSet->initialize($result ?? []);
        return $resultSet;
    }

    public function usingSearch(?string $role, array $pairs = [])
    {
        $where = new Where();

        if ($role) {
            $where->equalTo('role', $role);
        }
        foreach ($pairs as $identifier => $value) {
            $where
                ->nest()
                ->like($identifier, '%' . $value . '%')
                ->or
                ->like($this->userMetaTable->getTableName() . '.value', '%' . $value . '%')
                ->unnest();
        }

        $resultSet = $this->userTableGateway->getResultSetPrototype();
        $statement = $this->userTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->userTableGateway->getSql()
                    ->select()
                    ->join(
                        $this->userMetaTable->getTableName(),
                        $this->userTableGateway->getTable() . '.id = ' . $this->userMetaTable->getTableName() . '.user_id',
                        [],
                        Select::JOIN_LEFT
                    )
                    ->where($where)
                    ->group($this->userTableGateway->getTable(). '.id')
            );
        $userCollection = $statement->execute();
        foreach ($userCollection as $user) {
            $result[] = [
                $user,
                $this->userMetaTable->fetchByUserId($user['id']),
                $this->courseUserTable->fetchByUserId($user['id']),
                $this->tutorStudentCourseTable->fetchByStudentId($user['id'])
            ];
        }

        $resultSet->initialize($result ?? []);
        return $resultSet;
    }

    public function save(array $data): string
    {
        if (!empty($data['identity'])) {
            $data['identity'] = strtolower($data['identity']);
        }
        if (!empty($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }
        if ($this->duplicateIdentity($data['id'], $data['identity'])) {
            return 'duplicate_identity';
        }
        if (! empty($data['username']) && $this->duplicateUsername($data['id'], $data['username'])) {
            return 'duplicate_username';
        }
        if (! empty($data['pin']) && $this->duplicatePin($data['id'], $this->oldHashPassword($data['pin']))) {
            return 'duplicate_pin';
        }
        if (!$data['id']) {
            return $this->insert($data);
        }
        return $this->update($data['id'], $data);
    }

    public function duplicateIdentity($id, $identity)
    {
        $where = new Where();
        if ($id) {
            $where->notEqualTo('id', $id);
        }
        $where->EqualTo('identity', $identity);
        $statement = $this->userTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->userTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $userCollection = $statement->execute();
        if ($userCollection->count() > 0) {
            return true;
        }
        return false;
    }

    public function duplicateUsername($id, $username)
    {
        $where = new Where();
        if ($id) {
            $where->notEqualTo('id', $id);
        }
        $where->EqualTo('username', $username);
        $statement = $this->userTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->userTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $userCollection = $statement->execute();
        if ($userCollection->count() > 0) {
            return true;
        }
        return false;
    }

    public function duplicatePin($id, $pin)
    {
        $where = new Where();
        if ($id) {
            $where->notEqualTo('id', $id);
        }
        $where->EqualTo('pin', $pin);
        $statement = $this->userTableGateway->getSql()
            ->prepareStatementForSqlObject(
                $this->userTableGateway->getSql()
                    ->select()
                    ->where($where)
            );
        $userCollection = $statement->execute();
        if ($userCollection->count() > 0) {
            return true;
        }
        return false;
    }

    protected function insert(array $data): int
    {
        $this->userTableGateway->insert([
            'role' => $data['role'],
            'username' => $data['username'],
            'identity' => $data['identity'],
            'password' => $this->hashPassword($data['password']),
            'pin' => $this->oldHashPassword($data['pin']),
            'plainpin' => $data['pin'],
            'created_at' => time()
        ]);

        $lastInsertValue = $this->userTableGateway->lastInsertValue;

        foreach ($data['meta'] ?? [] as $name => $value) {
            $this->userMetaTable->add($lastInsertValue, $name, $value);
        }

        /**
         * @todo need to be decoupled
         */
        if (!empty($data['courses'])) {
            $this->courseUserTable->save($lastInsertValue, ...($data['courses'] ?? []));
        }

        /**
         * This is used for student
         * @todo need to be decoupled
         */
        if (!empty($data['courseTutor'])) {
            $new_data = array();
            foreach ($data['courseTutor'] as $info) {
                $new_data[$info['course']] =  $info;
            }
            $data['courseTutor'] = $new_data;
            foreach ($data['courseTutor'] as $courseTutor) {
                $courseIds[] = $courseTutor['course'];
            }
            $examcourseIds = array();
            foreach ($data['courseTutor'] as $courseExam) {
                if(array_key_exists('exam',$courseExam)) {
                    $examcourseIds[] = $courseExam['exam'][0];
                }
            }
            $messagetutorIds = array();
            foreach ($data['courseTutor'] as $messagetutor) {
                if(array_key_exists('messagetutor',$messagetutor)) {
                    $messagetutorIds[] = $messagetutor['messagetutor'][0];
                }
            }
            $certificateprintfreeIds = array();
            foreach ($data['courseTutor'] as $certificateprintfree) {
                if(array_key_exists('certificateprintfree',$certificateprintfree)) {
                    $certificateprintfreeIds[] = $certificateprintfree['certificateprintfree'][0];
                }
            }
            $this->courseUserTable->save($lastInsertValue, ...($courseIds ?? []));
            $this->tutorStudentCourseTable->save($lastInsertValue, ...($data['courseTutor'] ?? []));
            $this->examTriesTable->save($lastInsertValue, ...($examcourseIds ?? []));
            $this->messageTutorTable->save($lastInsertValue, ...($messagetutorIds ?? []));
            $this->certificatePrintFreeTable->save($lastInsertValue, ...($certificateprintfreeIds ?? []));
        }

        return $lastInsertValue;
    }

    protected function update(int $userId, array $data): int
    {
        $updateData = [
            'username' => $data['username'],
            'identity' => $data['identity'],
        ];

        if ($data['password']) {
            $updateData['password'] = $this->hashPassword($data['password']);
        }
        if ($data['pin']) {
            $updateData['pin'] = $this->oldHashPassword($data['pin']);
            $updateData['plainpin'] = $data['pin'];
        }
        $affectedRows = $this->userTableGateway->update($updateData, ['id' => $userId]);
        foreach ($data['meta'] ?? [] as $name => $value) {
            $oldMeta = $this->userMetaTable->getMetaByName($userId, $name);
            if ($oldMeta->count()) {
                $updateMeta = $this->userMetaTable->update($userId, $name, $value);
            } else {
                $updateMeta = $this->userMetaTable->add($userId, $name, $value);
            }
            $affectedRows += $updateMeta;
        }

        /**
         * This is used for tutor
         * @todo need to be decoupled
         */
        if (!empty($data['courses'])) {
            $affectedRows += $this->courseUserTable->save($userId, ...($data['courses'] ?? []));
        }

        /**
         * This is used for student
         * @todo need to be decoupled
         */
        if (!empty($data['courseTutor'])) {

            $new_data = array();
            foreach ($data['courseTutor'] as $info) {
                $new_data[$info['course']] =  $info;
            }
            $data['courseTutor'] = $new_data;

            foreach ($data['courseTutor'] as $courseTutor) {
                $courseIds[] = $courseTutor['course'];
            }

            $examcourseIds = array();
            foreach ($data['courseTutor'] as $courseExam) {
                if(array_key_exists('exam',$courseExam)) {
                    $examcourseIds[] = $courseExam['exam'][0];
                }
            }

            $messagetutorIds = array();
            foreach ($data['courseTutor'] as $messagetutor) {
                if(array_key_exists('messagetutor',$messagetutor)) {
                    $messagetutorIds[] = $messagetutor['messagetutor'][0];
                }
            }

            $certificateprintfreeIds = array();
            foreach ($data['courseTutor'] as $certificateprintfree) {
                if(array_key_exists('certificateprintfree',$certificateprintfree)) {
                    $certificateprintfreeIds[] = $certificateprintfree['certificateprintfree'][0];
                }
            }

            $affectedRows += $this->courseUserTable->save($userId, ...($courseIds ?? []));
            $affectedRows += $this->tutorStudentCourseTable->save($userId, ...($data['courseTutor'] ?? []));
            $affectedRows += $this->examTriesTable->save($userId, ...($examcourseIds ?? []));
            $affectedRows += $this->messageTutorTable->save($userId, ...($messagetutorIds ?? []));
            $affectedRows += $this->certificatePrintFreeTable->save($userId, ...($certificateprintfreeIds ?? []));
        }

        return $affectedRows;
    }

    public function generateToken($userId)
    {
        return sha1(md5($userId . '_lms_salt_' . time()));
    }

    public function updateToken($userId, $token)
    {
        $token_time = time();
        if (empty($token)) {
            $token_time = '';
        }
        $this->userTableGateway->update(['token' => $token, 'token_time' => $token_time], ['id' => $userId]);
    }

    public function updatePassword($userId, $password)
    {
        $this->userTableGateway->update(['password' => $this->hashPassword($password)], ['id' => $userId]);
    }

    /**
     * @deprecated used to ensure backward compatible
     * @param $password
     * @return string
     */
    public function oldHashPassword($password)
    {
        return sha1(md5($password));
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}