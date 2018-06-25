<?php

namespace Assignment\Action\View\Work;

use Assignment\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rbac;
use User\Model\User;
use User\Model\UserTable;
use User\Model\UserMetaTable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Template;

class ResultSet
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var UserMetaTable
     */
    private $userMetaTable;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\AssignmentWorkTable $assignmentWorkTable,
        UserTable $userTable,
        UserMetaTable $userMetaTable
    ) {
        $this->template = $template;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var RouteResult $routeResult
         * @var User $user
         */
        $routeResult = $request->getAttribute(RouteResult::class);
        $selectedTutor = $request->getQueryParams()['tutor'] ?? null;
        $user = $request->getAttribute(User::class);
        $filter = $request->getAttribute('filter');
        if ('all' == $filter) {
            $filter = NULL;
        }
        if (Rbac\Role\Administrator::class === $user->getRole()) {
            if ($selectedTutor) {
                $assignmentWorkResultSet = $this->assignmentWorkTable->byTutorUsingFilter($selectedTutor, $filter);
            } else {
                $assignmentWorkResultSet = $this->assignmentWorkTable->usingFilter($filter);
            }
            $tutorsMeta = [];
            foreach ($this->userTable->byRole(Rbac\Role\Tutor::class) as $tutor) {
                $tutorsMeta[$tutor->getId()] = $this->userMetaTable->fetchByUserId($tutor->getId())->toArray();
            }
            return new HtmlResponse($this->template->render('assignment::work/resultset', [
                'assignmentWorkResultSet' => $assignmentWorkResultSet,
                'matchedRouteName' => $routeResult->getMatchedRouteName(),
                'tutorResultSet' => $this->userTable->byRole(Rbac\Role\Tutor::class),
                'tutorsMeta' => $tutorsMeta,
                'selectedTutor' => $selectedTutor,
                'isAdmin' => true
            ]));
        }

        $assignmentWorkResultSet = $this->assignmentWorkTable->byTutorUsingFilter($user->getId(), $filter);
       # throw new \Exception(var_dump($assignmentWorkResultSet));
        return new HtmlResponse($this->template->render('assignment::work/resultset', [
            'assignmentWorkResultSet' => $assignmentWorkResultSet,
            'matchedRouteName' => $routeResult->getMatchedRouteName()
        ]));
    }
}
