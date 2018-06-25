<?php

namespace Certificate\Model;

use Certificate;
use Content;
use Course\Model\ContentTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Course\Model\CourseTableGateway;
use Course\Model\CourseUserTableGateway;

class CertificateDeliveryTable
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

    public function __construct(CertificateDeliveryTableGateway $tableGateway, ContentTableGateway $contenttableGateway, CourseTableGateway $coursetableGateway, CourseUserTableGateway $courseusertableGateway)
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

    public function fetchByPaymentId(int ...$ids): ?ResultSet
    {
        if (!$ids) {
            return null;
        }

        return $this->tableGateway->select(['payment_id' => $ids]);
    }

    public function save($data)
    {
		if (!empty($data['id'])) {
			return $this->tableGateway->update([
				'payment_id' => $data['payment_id'],
				'status_sent' => $data['status_sent']
			], ['id' => $data['id']]);
        }
		
		$this->insert($data);
    }

    protected function insert($data)
    {
        $this->tableGateway->insert([
				'payment_id' => $data['payment_id'],
				'status_sent' => $data['status_sent']
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