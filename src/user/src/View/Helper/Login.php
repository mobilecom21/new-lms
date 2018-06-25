<?php

namespace User\View\Helper;

#use User\Model;
use Zend\Session\Container;

class Login
{
    /**
     * @var Model\UserTable
     */
    #private $userTable;

    public function __construct(
        #Model\UserTable $userTable
    )
    {
        #$this->userTable = $userTable;
    }

    #public function __invoke(int $userId): Model\User
    public function __invoke()
    {
        $originalUser = new Container('OriginalUser');

        $userChainCount = 0;
        $userChain = array();
        if(is_array($originalUser->userChain)) {
            $userChainCount = count($originalUser->userChain);
            foreach($originalUser->userChain as $user) {
                $userChain[] = [
                    'id' => $user->getId(),
                    'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                    'username' => $user->getUsername()
                ];
            }
        }

        return [
            'userChainCount' => $userChainCount,
            'userChain' => $userChain,
            'topLevelRole' => $originalUser->topLevelRole
        ];
    }
}