<?php

namespace User\Action\View\ResultSet;

use User\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Course\Model\CourseTable;

class ByUserRole
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
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $singleRouteName;

    /**
     * @var string
     */
    private $rbac;

    /**
     * @var string
     */
    private $loggedId;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\UserTable $userTable,
        string $rbac,
        int $loggedId,
        string $singleRouteName,
        string $templateName,
        CourseTable $courseTable
    ) {

        $this->template = $template;
        $this->userTable = $userTable;
        $this->rbac = $rbac;
        $this->loggedId = $loggedId;
        $this->templateName = $templateName;
        $this->singleRouteName = $singleRouteName;
        $this->courseTable = $courseTable;
    }

    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }


    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {

        //6.26mb memory usage

        $search = $request->getQueryParams()['search'] ?? [];
        $tutorResultSet = $this->userTable->byRole('Rbac\Role\Tutor');
        $tutors = array();
        foreach($tutorResultSet as $tutor) {
            $tutors[$tutor->getId()] = $this->userTable->oneById($tutor->getId())->getFirstName() . ' ' . $this->userTable->oneById($tutor->getId())->getLastName();
        }

        //6.61mb memory usage
        

        $courses = array();
        foreach($this->courseTable->fetchAll() as $course) {
            $courses[$course->getId()] = $course->getName();
        }

        //6.63mb memory usage
    try {
            $resultSet = $this->userTable->byRoleAndRbac('Rbac\Role\Student', $this->rbac, $this->loggedId, $search);
            //MEMORY LIMIT EXCEEDED
            //throw new \Exception($this->convert(memory_get_usage()));

            return new HtmlResponse($this->template->render($this->templateName, [
                'resultSet' => $resultSet,
                'search' => $search,
                'singleRouteName' => $this->singleRouteName,
                'userTable' => $this->userTable,
                'tutors' => $tutors,
                'courses' => $courses
            ]));
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }

}
}
