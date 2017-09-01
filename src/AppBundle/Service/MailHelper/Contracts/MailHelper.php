<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 01.09.2017
 * Time: 14:45
 */

namespace AppBundle\Service\MailHelper\Contracts;


use Symfony\Component\DependencyInjection\Container;

interface MailHelper
{
    public function createMessage($text, $receiver);

    public function send();
}