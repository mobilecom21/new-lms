<?php

namespace User\Action\Form;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Model;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use Zend\Form\FormInterface;

class User
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
     * @var FormInterface
     */
    private $form;

    /**
     * @var string
     */
    private $templateName;

    public function __construct(
        Template\TemplateRendererInterface $template,
        Model\UserTable $userTable,
        FormInterface $form,
        string $templateName = 'user::form'
    ) {

        $this->template = $template;
        $this->userTable = $userTable;
        $this->form = $form;
        $this->templateName = $templateName;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $id = $request->getAttribute('id');

        if ($id) {
            /**
             * @var Model\User $user
             */
            $user = $this->userTable->oneById($id);
        }

        // bind object when id passed in url
        if (isset($user) && $user instanceof Model\User) {
            $this->form->setData($user->getArrayCopy());
        }

        return new HtmlResponse($this->template->render($this->templateName, [
            'form' => $this->form
        ]));
    }
}
