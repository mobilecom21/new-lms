<?php

namespace Message\Model;

use User\Model\UserOnlineTable;

class MessageTable
{
    /**
     * @var MessageTableGateway
     */
    private $tableGateway;

    /**
     * @var UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var UserOnlineTable
     */
    private $userOnlineTable;

    public function __construct(MessageTableGateway $tableGateway, UserOnlineTable $userOnlineTable)
    {
        $this->tableGateway = $tableGateway;
        $this->userOnlineTable = $userOnlineTable;
    }

    public function fetchReceived(int $receiver, string $search)
    {
        $statement = $this->tableGateway->getAdapter()->query("
            SELECT
                um_first_name.value  as first_name,
                um_last_name.value as last_name,
                uo.user_id as status,
                m.*
            FROM `message` m
            LEFT JOIN `user_meta` um_first_name ON um_first_name.user_id = m.sender AND um_first_name.name = 'first_name'
            LEFT JOIN `user_meta` um_last_name ON um_last_name.user_id = m.sender AND um_last_name.name = 'last_name'
            LEFT JOIN `user_online` uo ON uo.user_id = m.sender
            WHERE m.receiver = '$receiver' AND m.text LIKE '%$search%'
            ORDER BY m.id DESC
        ");

        $resultSet = $this->tableGateway->getResultSetPrototype();
        return $resultSet->initialize($statement->execute());
    }

    public function fetchSent(int $sender, string $search)
    {
        $statement = $this->tableGateway->getAdapter()->query("
            SELECT
                um_first_name.value  as first_name,
                um_last_name.value as last_name,
                uo.user_id as status,
                m.*
            FROM `message` m
            LEFT JOIN `user_meta` um_first_name ON um_first_name.user_id = m.receiver AND um_first_name.name = 'first_name'
            LEFT JOIN `user_meta` um_last_name ON um_last_name.user_id = m.receiver AND um_last_name.name = 'last_name'
            LEFT JOIN `user_online` uo ON uo.user_id = m.receiver
            WHERE m.sender = '$sender' AND m.text LIKE '%$search%'
        ");

        $resultSet = $this->tableGateway->getResultSetPrototype();
        return $resultSet->initialize($statement->execute());
    }

    public function countUnread(int $receiver)
    {
        $select = $this->tableGateway->getSql()->select()
            ->where([
                'receiver = ?' => $receiver,
                'viewed = ?' => 0,
            ]);
        return count($this->tableGateway->selectWith($select));
    }

    public function viewed($id, $receiver)
    {
        return $this->tableGateway->update(['viewed' => 1], ['id' => $id, 'receiver' => $receiver]);
    }

    public function delete($currentUserId, $userId)
    {
        $this->tableGateway->update(['hide_to_receiver' => 1], ['receiver' => $currentUserId, 'sender' => $userId]);
        $this->tableGateway->update(['hide_to_sender' => 1], ['sender' => $currentUserId, 'receiver' => $userId]);
        $this->tableGateway->update(['viewed' => 1], ['receiver' => $currentUserId, 'sender' => $userId]);
    }

    public function insert(array $input)
    {
        $this->tableGateway->insert([
            'sender' => $input['sender'],
            'receiver' => $input['receiver'],
            'text' => $input['text'],
            'created_at' => time()
        ]);

        return $this->tableGateway->lastInsertValue;
    }
}