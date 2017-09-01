<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 01.09.2017
 * Time: 14:47
 */

namespace AppBundle\Service\MailHelper\VkMailHelper;


use AppBundle\Service\MailHelper\Contracts\MailHelperFactory;
use Symfony\Component\DependencyInjection\Container;

class VkMailHelperFactory implements MailHelperFactory
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create()
    {
        return new VkMailHelper($this->container);
    }
}