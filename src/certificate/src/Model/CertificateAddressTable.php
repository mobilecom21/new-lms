<?php

namespace Certificate\Model;

use Certificate;
use Content;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Course\Model\CourseTableGateway;
use Course\Model\CourseUserTableGateway;

class CertificateAddressTable
{
    /**
     * @var CertificateAddressTableGateway
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

    public function __construct(CertificateAddressTableGateway $tableGateway, ContentTableGateway $contenttableGateway, CourseTableGateway $coursetableGateway, CourseUserTableGateway $courseusertableGateway)
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

    public function fetchByUserId(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['user_id' => $ids]);
    }

    public function save($data)
    {
		if (!empty($data['id'])) {
			return $this->tableGateway->update([
				'address' => $data['address'],
				'address2' => $data['address2'],
				'city' => $data['city'],
				'state_id' => $data['state_id'],
				'country_id' => $data['country_id'],
				'phone' => $data['phone'],
				'postal_code' => $data['postal_code']
			], ['id' => $data['id']]);
        }
		
		$this->insert($data);
    }

    protected function insert($data)
    {
        $this->tableGateway->insert([
			'user_id' => $data['user_id'],
            'address' => $data['address'],
            'address2' => $data['address2'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
			'phone' => $data['phone'],
			'postal_code' => $data['postal_code'],
        ]);

        return $this->tableGateway->lastInsertValue;
    }

    protected function update($data)
    {
        $this->tableGateway->update([
			'user_id' => $data['user_id'],
            'address' => $data['address'],
            'address2' => $data['address2'],
			'city' => $data['city'],
			'state_id' => $data['state_id'],
			'country_id' => $data['country_id'],
			'phone' => $data['phone'],
			'postal_code' => $data['postal_code'],
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