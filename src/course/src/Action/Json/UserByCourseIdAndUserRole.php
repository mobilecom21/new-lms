<?php

namespace Course\Action\Json;

use Course\Model;
use User;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class UserByCourseIdAndUserRole
{
    /**
     * @var Model\CourseUserTable
     */
    private $courseUserTable;

    /**
     * @var User\Model\UserMetaTable
     */
    private $userMetaTable;

    public function __construct(Model\CourseUserTable $courseUserTable, User\Model\UserMetaTable $userMetaTable)
    {
        $this->courseUserTable = $courseUserTable;
        $this->userMetaTable = $userMetaTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $parsedBody = $request->getParsedBody();

        $courseId = $parsedBody['courseId'] ?? null;
        $role = $parsedBody['role'] ?? null;

        $users = $this->courseUserTable->fetchUserByCourseIdAndUserRole($courseId, $role)->toArray();
        foreach ($users as $key => $user) {
            $userId = $user['id'];
            $userMetas = $this->userMetaTable->fetchByUserId($userId)->toArray();
            $first_name = $last_name = '';
            foreach($userMetas as $userMeta) {
                if ('first_name' == $userMeta['name']) {
                    $first_name = $userMeta['value'];
                } elseif ('last_name' == $userMeta['name']) {
                    $last_name = $userMeta['value'];
                }
            }
            $users[$key]['text'] = $first_name . ' ' . $last_name;
        }
        return new JsonResponse($users);
    }
}
