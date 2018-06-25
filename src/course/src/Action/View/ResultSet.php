<?php

namespace Course\Action\View;

use Course\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

use Rbac;
use User\Model\User;
use User\Model\UserTable;
use User\Model\UserMetaTable;
use Course\Model\CourseTable;
use Course\Model\CourseUserTable;

class ResultSet
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CourseTable
     */
    private $courseTable;

    /**
     * @var Model\CourseUserTable
     */
    private $courseUserTable;

    /**
     * @var Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\UserMetaTable
     */
    private $userMetaTable;

    public function __construct(
        Template\TemplateRendererInterface $template,
        UserTable $userTable,
        UserMetaTable $userMetaTable,
        Model\CourseTable $courseTable,
        Model\CourseUserTable $courseUserTable
        )
    {
        $this->template = $template;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->courseTable = $courseTable;
        $this->courseUserTable = $courseUserTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        //fetch filter info from GET request if available
        $routeResult = $request->getAttribute(RouteResult::class);
        $selectedTutor = $request->getQueryParams()['tutor'] ?? null;
        $user = $request->getAttribute(User::class);
        $filter = $request->getAttribute('filter');
        if (empty($filter) || $filter != 'archived') {
            $archived = 0;
        } else if ($filter == 'archived') {
            $archived = 1;
        }

        //if we're an administrator, allow the filtering to happen
        if (Rbac\Role\Administrator::class === $user->getRole()) {
            //fetch relevant result set dependant on whether the filter is applied
            if ($selectedTutor) {
                $courseTutorResultSet = $this->courseUserTable->fetchByUserId($selectedTutor, $archived);
            } else {
                $courseTutorResultSet = $this->courseTable->fetchByFilter($archived);
            }

            //getch the meta data for a human-readable dropdown
            $tutorsMeta = [];
            foreach ($this->userTable->byRole(Rbac\Role\Tutor::class) as $tutor) {
                $tutorsMeta[$tutor->getId()] = $this->userMetaTable->fetchByUserId($tutor->getId())->toArray();
            }

            //render the page
            return new HtmlResponse($this->template->render('course::resultset', [
                'resultSet' => $courseTutorResultSet,
                'tutorResultSet' => $this->userTable->byRole(Rbac\Role\Tutor::class),
                'tutorsMeta' => $tutorsMeta,
                'selectedTutor' => $selectedTutor,
                'archived' => $archived,
            ]));
        }


        //not an admin, just render the page
        return new HtmlResponse($this->template->render('course::resultset', [
            'resultSet' => $this->courseTable->fetchByFilter(),
        ]));
    }
}
