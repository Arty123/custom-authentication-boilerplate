<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 01.09.2017
 * Time: 14:48
 */

namespace AppBundle\Service\MailHelper\VkMailHelper;


use AppBundle\Service\MailHelper\Contracts\MailHelper;
use Symfony\Component\DependencyInjection\Container;

class VkMailHelper implements MailHelper
{
    private $container;

    private $message;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createMessage($plainPassword, $mailTo)
    {
        $message = (new \Swift_Message('Hello message'))
            ->setFrom('send@example.com')
            ->setTo($mailTo)
            ->setBody(
                $this->container->get('templating')->render(
                    '@App/emails/registration.html.twig',
                    array('password' => $plainPassword)
                ),
                'text/html'
            )
        ;

        $this->message = $message;

        return $this;
    }

    public function send()
    {
        $this->container->get('mailer')->send($this->message);
    }
}