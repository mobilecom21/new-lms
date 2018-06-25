<?php

namespace Course\Action\View\ResultSet;

use Course\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class ByUserId
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CourseUserTable
     */
    private $courseUserTable;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $singleRouteName;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\CourseUserTable $courseUserTable,
        int $userId,
        string $singleRouteName,
        string $templateName = 'course::resultset'
    ) {

        $this->template = $template;
        $this->courseUserTable = $courseUserTable;
        $this->userId = $userId;
        $this->templateName = $templateName;
        $this->singleRouteName = $singleRouteName;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return new HtmlResponse($this->template->render($this->templateName, [
            'resultSet' => $this->courseUserTable->fetchByUserId($this->userId),
            'singleRouteName' => $this->singleRouteName
        ]));
    }
}
