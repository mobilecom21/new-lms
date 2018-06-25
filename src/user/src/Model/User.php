<?php

namespace User\Model;

use Course;
use Tutor;
use Zend\Filter\Word\UnderscoreToCamelCase;

class User
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $identity;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $pin;

    /**
     * @var string
     */
    protected $plainpin;

    /**
     * @var int
     */
    protected $createdAt;

    /**
     * @var int
     */
    protected $markingDays;

    /**
     * @var UserMeta[]
     */
    protected $meta;

    /**
     * @var array
     */
    protected $courses;

    /**
     * @var array
     */
    protected $courseTutor;

    /**
     * @var array
     */
    protected $metaInCamelCase;

    /**
     * @param array $input
     */
    public function exchangeArray(array $input)
    {
        if (empty($input[0])) {
            $this->setId($input['id']);
            $this->setRole($input['role']);
            $this->setUsername($input['username']);
            $this->setIdentity($input['identity']);
            $this->setPassword($input['password']);
            $this->setPin($input['pin']);
            $this->setPlainpin($input['plainpin']);
            return;
        }
        $this->setId($input[0]['id']);
        $this->setRole($input[0]['role']);
        $this->setUsername($input[0]['username']);
        $this->setIdentity($input[0]['identity']);
        $this->setPassword($input[0]['password']);
        $this->setPin($input[0]['pin']);
        $this->setPlainpin($input[0]['plainpin']);

        $underscoreToCamelCase = new UnderscoreToCamelCase();

        /**
         * @var UserMeta $meta
         */
        foreach ($input[1] ?? [] as $meta) {
            $name = $underscoreToCamelCase->filter($meta->getName());

            $this->meta[$meta->getName()] = $meta->getValue();
            $this->metaInCamelCase[$name] = $meta->getValue();
        }

        /**
         * @var Course\Model\Course $course
         */
        foreach ($input[2] ?? [] as $course) {
            $this->courses[] = $course->getId();
        }

        /**
         * @var Tutor\Model\TutorStudentCourse $tutorStudentCourse
         */
        foreach ($input[3] ?? [] as $tutorStudentCourse) {
            $this->courseTutor[] = [
                'course' => $tutorStudentCourse->getCourseId(),
                'tutor' => $tutorStudentCourse->getTutorId(),
                'end_of_support' => $tutorStudentCourse->getEndOfSupport(),
                'HLN' => $tutorStudentCourse->getHomeLearningNumber()
            ];
        }
    }

    /**
     * Get user meta
     *
     * @param $name
     * @param $arguments
     *
     * @return null|string
     */
    public function __call(string $name, array $arguments): ?string
    {
        if (false === strpos($name, 'get', 0)) {
            return null;
        }

        $name = str_replace('get', '', $name);
        return $this->metaInCamelCase[$name] ?? null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return User
     */
    public function setId(string $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function setRole(string $role): User
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @param string $identity
     *
     * @return User
     */
    public function setIdentity(string $identity): User
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @return string
     */
    public function getPin(): string
    {
        return $this->pin;
    }

    /**
     * @param string $pin
     *
     * @return User
     */
    public function setPin(string $pin): User
    {
        $this->pin = $pin;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlainpin(): string
    {
        return $this->plainpin;
    }

    /**
     * @param string $plainpin
     *
     * @return User
     */
    public function setPlainpin(string $plainpin): User
    {
        $this->plainpin = $plainpin;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int
     */
    public function getMarkingDays(): int
    {
        return $this->markingDays;
    }

    /**
     * @param int $markingDays
     *
     * @return User
     */
    public function setMarkingDays(int $markingDays): User
    {
        $this->markingDays = $markingDays;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     *
     * @return User
     */
    public function setCreatedAt(int $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Return array for object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}