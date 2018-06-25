<?php

namespace Course\Action\Form;

use Course\Form;
use Course\Model;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class Course
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var Model\CourseTable
     */
    private $courseTable;

    public function __construct(Template\TemplateRendererInterface $template, Model\CourseTable $courseTable)
    {
        $this->template = $template;
        $this->courseTable = $courseTable;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $courseId = $request->getAttribute('id');

        if ($courseId) {
            /**
             * @var Model\Course
             */
            $course = $this->courseTable->fetchById($courseId);
        }

        $form = new Form\Course();

        // bind object when id passed in url
        if (isset($course) && $course instanceof Model\Course) {
            $form->bind($course);
        }

        return new HtmlResponse($this->template->render('course::form', [
            'form' => $form
        ]));
    }
}
