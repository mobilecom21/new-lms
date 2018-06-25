<?php

namespace User\Action\Post;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Model;
use Zend\Authentication;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class Authenticate
{
    /**
     * @var Authentication\AuthenticationService
     */
    private $authenticationService;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var Model\UserLoginTable
     */
    private $userLoginTable;

    public function __construct(
        Authentication\AuthenticationService $authenticationService,
        UrlHelper $urlHelper,
        Model\UserTable $userTable,
        Model\UserMetaTable $userMetaTable,
        Model\UserLoginTable $userLoginTable
    )
    {
        $this->authenticationService = $authenticationService;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->userLoginTable = $userLoginTable;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];

        $defaultJsonResponse = new JsonResponse([
            'errors' => [
                'identity' => [],
                'password' => []
            ],
            'errorMessage' => 'Login failed'
        ]);

        if (isset($data['pin'])) {
            $defaultJsonResponse = new JsonResponse([
                'errors' => [
                    'pin' => []
                ],
                'errorMessage' => 'Login failed'
            ]);
        }

        if ((empty($data['identity']) || empty($data['password'])) && empty($data['pin'])) {
            return $defaultJsonResponse;
        }

        /**
         * @var Authentication\Adapter\DbTable\CallbackCheckAdapter $adapter
         */
        $adapter = $this->authenticationService->getAdapter();
        if (! empty($data['pin'])) {
            $adapter->setIdentityColumn('pin')->setCredentialColumn('pin');
            $adapter->setIdentity($this->userTable->oldHashPassword($data['pin']));
            $adapter->setCredential($this->userTable->oldHashPassword($data['pin']));
        } elseif (false !== strpos($data['identity'], '@')) {
            $adapter->setIdentityColumn('identity')->setCredentialColumn('password');
            $adapter->setIdentity($data['identity']);
            $adapter->setCredential($data['password']);
        } else {
            $adapter->setIdentityColumn('username')->setCredentialColumn('password');
            $adapter->setIdentity($data['identity']);
            $adapter->setCredential($data['password']);
        }

        $adapter->setCredentialValidationCallback(function ($savedHash, $input) use ($data) {
            if (!empty($data['pin'])) {
                return $savedHash === $input;
            } else if ($savedHash === $this->userTable->oldHashPassword($input)) {
                return true;
            } else {
                return password_verify($input, $savedHash);
            }
        });

        $authenticationResult = $this->authenticationService->authenticate($adapter);

        if ($authenticationResult->isValid()) {
            $resultRowObject = $adapter->getResultRowObject();
            $suspended = $this->userMetaTable->getMetaByName($resultRowObject->id, 'suspended')->current();
            if ($suspended && 'yes' == $suspended->getValue()) {
                $this->authenticationService->clearIdentity();
                return new JsonResponse([
                    'errors' => [
                        'identity' => [],
                        'password' => []
                    ],
                    'errorMessage' => 'Account suspended'
                ]);
            }
            $storage = $this->authenticationService->getStorage();
            $servParam = $request->getServerParams();
            $this->userLoginTable->add($resultRowObject->id, $servParam['REMOTE_ADDR']);
            $storage->write($this->userTable->oneById($resultRowObject->id));
            return new JsonResponse([
                'redirectTo' => ($this->urlHelper)('home')
            ]);
        }

        return $defaultJsonResponse;
    }
}
