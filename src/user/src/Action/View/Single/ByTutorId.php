<?php

namespace User\Action\View\Single;

use User\Model;
use Assignment\Model\AssignmentWorkTable;
use Tutor\Model\TutorStudentCourseTable;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class ByTutorId
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\userMetaTable
     */
    private $userMetaTable;

    /**
     * @var AssignmentWorkTable
     */
    private $assignmentWorkTable;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $currentUserRole;

    /**
     * @var int
     */
    private $currentUserId;

    /**
     * @var string
     */
    private $singleRouteName;

    /**
     * @var string
     */
    private $templateNamespace;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\UserTable $userTable,
        Model\UserMetaTable $userMetaTable,
        TutorStudentCourseTable $tutorStudentCourseTable,
        AssignmentWorkTable $assignmentWorkTable,
        int $userId,
        string $role,
        string $singleRouteName,
        string $templateName
    ) {

        $this->template = $template;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->tutorStudentCourseTable = $tutorStudentCourseTable;
        $this->assignmentWorkTable = $assignmentWorkTable;
        $this->currentUserRole = $role;
        $this->currentUserId = $userId;
        $this->singleRouteName = $singleRouteName;
        $this->templateName = $templateName;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var string
         */
        $id = $request->getAttribute('id');
        
        /**
         * @var Model\Course $course
         */
        $user = $this->userTable->oneById($id);

        if (false === $user) {
            return $delegate->process($request);
        }
        return new HtmlResponse($this->template->render($this->templateName, [
            'user' => $user,
            'usermeta' => $this->userMetaTable->fetchByUserId($id),
            'role' => $this->currentUserRole
        ]));
    }
}
