<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 31.08.2017
 * Time: 16:33
 */

namespace AppBundle\Service\OauthClientAuthentication\VkClient;

use AppBundle\Service\OauthClientAuthentication\VkClient\OauthVkClient;
use AppBundle\Service\OauthClientAuthentication\Contracts\OauthClientFactory;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class OauthVkClientFactory extends OauthClientFactory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Container $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function create()
    {
        return new OauthVkClient($this->container, $this->request);
    }
}