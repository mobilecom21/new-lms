<?php
namespace Student\User\Factory\Action\View\ResultSet;
use User;
use Psr\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template;
use Course;
class ByUserRole
{

    public function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }


    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);
        //5.82mb memory usage

        /**
         * @var User\Model\UserTable $userTable
         */
        $userTable = $container->get(User\Model\UserTable::class);
        //6.26mb memory usage

        /**
         * @var Course\Model\CourseTable $courseTable
         */
        $courseTable = $container->get(Course\Model\CourseTable::class);
        //6.27mb memory usage

        /**
         * @var AuthenticationService $authenticationService
         */
        $authenticationService = $container->get(AuthenticationService::class);
        //6.27mb memory usage

        /**
         * @var User\Model\User $user
         */
        $user = $authenticationService->getStorage()->read();
        //6.27mb memory usage

        return new User\Action\View\ResultSet\ByUserRole(
            $template,
            $userTable,
            $user->getRole(),
            $user->getId(),
            'student/user/view/resultset',
            'student::resultset',
            $courseTable
        );
    }
}