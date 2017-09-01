<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 31.08.2017
 * Time: 16:09
 */

namespace AppBundle\Service\OauthClientAuthentication\Contracts;


abstract class OauthClientFactory
{
    public abstract function createClient();
}