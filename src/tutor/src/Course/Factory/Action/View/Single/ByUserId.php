<?php

namespace Tutor\Course\Factory\Action\View\Single;

use Course;
use Psr\Container\ContainerInterface;
use User;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template;
use Exclusive;
use Exam;

class ByUserId
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $template = $container->get(Template\TemplateRendererInterface::class);

        /**
         * @var Course\Model\CourseUserTable $contentUserTable
         */
        $contentUserTable = $container->get(Course\Model\CourseUserTable::class);

        /**
         * @var Course\Model\ContentTable $contentTable
         */
        $contentTable = $container->get(Course\Model\ContentTable::class);


        /**
         * @var Exclusive\Model\MessageTutorTable $messageTutorTable
         */
        $messageTutorTable = $container->get(Exclusive\Model\MessageTutorTable::class);

        /**
         * @var Exclusive\Model\CertificatePrintFreeTable $certificatePrintFreeTable
         */
        $certificatePrintFreeTable = $container->get(Exclusive\Model\CertificatePrintFreeTable::class);

        /**
         * @var User\Model\UserMetaTable $usermetaTable
         */
        $usermetaTable = $container->get(User\Model\UserMetaTable::class);

        /**
         * @var Exam\Model\ExamTable $examTable
         */
        $examTable = $container->get(Exam\Model\ExamTable::class);

        /**
         * @var AuthenticationService $authenticationService
         */
        $authenticationService = $container->get(AuthenticationService::class);

        /**
         * @var User\Model\User $user
         */
        $user = $authenticationService->getStorage()->read();

        return new Course\Action\View\Single\ByUserId(
            $template,
            $contentUserTable,
            $contentTable,
            $messageTutorTable,
            $certificatePrintFreeTable,
            $usermetaTable,
            $examTable,
            $user->getId(),
            true,
            null,
            'tutor'
        );
    }
}