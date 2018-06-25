<?php

namespace User\Action\Post;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Model;
use Options;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Expressive\Template;

class Reset
{

    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var Model\UserTable
     */
    private $userTable;

    /**
     * @var Model\UserMetaTable
     */
    private $userMetaTable;

    /**
     * @var Options
     */
    private $optionsTable;

    /**
     * @var Mail\Transport\TransportInterface
     */
    private $transportMail;

    public function __construct(
        Template\TemplateRendererInterface $template,
        UrlHelper $urlHelper,
        Model\UserTable $userTable,
        Model\UserMetaTable $userMetaTable,
        Options\Model\OptionsTable $optionsTable,
        Mail\Transport\TransportInterface $transportMail
    )
    {
        $this->template = $template;
        $this->urlHelper = $urlHelper;
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
        $this->optionsTable = $optionsTable;
        $this->transportMail = $transportMail;
    }

    public function __invoke(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = $request->getParsedBody() ?? [];
        if (empty($data['identity'])) {
            return new JsonResponse(['errors' => ['identity' => ['Empty E-mail or Username']]]);
        }
        if ((false !== strpos($data['identity'], '@'))) {
            $users = $this->userTable->byIdentity($data['identity'])->toArray();
        } else {
            $users = $this->userTable->byUsername($data['identity'])->toArray();
        }

        if (0 == count($users)) {
            return new JsonResponse(['successMessage' => 'Password reset instructions were sent to your email']);
        }

        $user = reset($users);
        $token = $this->userTable->generateToken($user['id']);
        $this->userTable->updateToken($user['id'], $token);

        $first_name = $this->userMetaTable->getMetaByName($user['id'], 'first_name')->current();
        $last_name = $this->userMetaTable->getMetaByName($user['id'], 'last_name')->current();

        $htmlMarkup = $this->template->render('emails::reset', [
            'layout' => false,
            'fullName' => $first_name->getValue() . ' ' .  $last_name->getValue(),
            'resetLink' => $request->getUri()->getHost() . ($this->urlHelper)('user/form/newpass') . '?pin=' . $token
        ]);
        $html = new MimePart($htmlMarkup);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        if ($this->optionsTable->optionExists('from_email')) {
            $from = $this->optionsTable->fetchByName('from_email')['value'];
            $message = new Mail\Message();
            $message->addTo($user['identity'])
                ->addFrom($from)
                ->setSubject('Resetting your password instructions')
                ->setBody($body);
            $this->transportMail->send($message);
        }

        return new JsonResponse(['successMessage' => 'Password reset instructions were sent to your email']);
    }
}