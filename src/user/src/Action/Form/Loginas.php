<?php

namespace User\Action\Form;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User;
use Tutor;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Diactoros\Response\JsonResponse;

class Loginas
{

    public function __construct(
            User\Model\UserTable $userTable,
            Tutor\Model\TutorStudentCourseTable $tutorStudentCourseTable,
            AuthenticationService $authenticationService,
            UrlHelper $urlHelper
        )
    {
        $this->userTable = $userTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->authenticationService = $authenticationService;
        $this->urlHelper = $urlHelper;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        //for checking if something went wrong later
        $errorMessage = false;

        //get userId from URL
        $userId = (int) $request->getAttribute('userId');

        if($userId > 0) {
            //get who we're logged in as at the moment
            $currentUser = $request->getAttribute(User\Model\User::class);

            if(!is_null($currentUser)) {

                //open up $_SESSION['currentUser'] (the Zend way)
                $originalUser = new Container('OriginalUser');

                //check to see currentUser exists, otherwise initialise it and set logged in user 
                if(is_null($originalUser->currentUser) || array() == $originalUser->currentUser) {
                    $originalUser->currentUser = $currentUser;
                    $originalUser->topLevelRole = $currentUser->getRole();
                }

                //get newUser information
                $newUser = $this->userTable->oneById($userId);
                $loginAsUser = false;


                //see whether currentUser has permission to login an as newUser
                //NOTE admin cannot login as another admin
                if($originalUser->currentUser->getId() == $newUser->getId()) {
                    $errorMessage = "You cannot log in as yourself";
                    
                } else if($originalUser->currentUser->getRole() == "Rbac\\Role\\Administrator" && $newUser->getRole() == "Rbac\\Role\\Administrator") {
                    $errorMessage = "Administrators cannot login as other administrators";

                } else if($originalUser->currentUser->getRole() == "Rbac\\Role\\Administrator") {
                    //LOGIN AS USER
                    $loginAsUser = true;

                } else if($originalUser->currentUser->getRole() == "Rbac\\Role\\Tutor") {
                    
                    if("Rbac\\Role\\Administrator" == $originalUser->topLevelRole) {
                        //NOTE - the below line is if you want to allow tutors to be able to log in as their own students (Pete)
                        //$this->tutorStudentCourseTable->isTutorForStudent($originalUser->currentUser->getId(), $userId)
                        //LOGIN AS USER
                        $loginAsUser = true;
                    } else {
                        $errorMessage = "You can only log in as one of your own students";
                    }

                } else if ($originalUser->currentUser->getRole() == "Rbac\\Role\\Student") {
                    $errorMessage = "You do not have permission to log in as another user";
                }


                if($loginAsUser) {

                    //add the current logged in user to the login chain
                    if(is_null($originalUser->userChain) || array() == $originalUser->userChain) {
                        $originalUser->userChain = array($originalUser->currentUser);#
                        $originalUser->currentUser = $newUser;
                    } else {
                        //make sure we don't add the same user more than once to the session array
                        $add = true;
                        foreach($originalUser->userChain as $user) {
                            if($user->getId() == $originalUser->currentUser->getId()) {
                                $add = false;
                            }
                        }

                        if($add) {
                            $originalUser->userChain[] = $currentUser;
                        }
                        $originalUser->currentUser = $newUser;
                    }

                    //log existing user out
                    $this->authenticationService->clearIdentity();
                    //push new user to the login state
                    $storage = $this->authenticationService->getStorage();
                    $storage->write($newUser);

                    return new RedirectResponse(($this->urlHelper)('home'));
                }

            } else {
                $errorMessage = "You are not logged in";
            }

            
        } else {
            $errorMessage = "No valid user ID supplied";
        }

        return new JsonResponse(['errorMessage' => $errorMessage]);

    }
}
