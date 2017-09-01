<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 31.08.2017
 * Time: 16:08
 */

namespace AppBundle\Service\OauthClientAuthentication\Contracts;


abstract class OauthClient
{
    public abstract function authorize(array $scope = []);

    public abstract function getAccessToken($code = '');

    public abstract function apiRequest($methodName = '', $userData, array $parameters = []);
}