<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 30.08.2017
 * Time: 15:05
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VkConnectController extends Controller
{
    /**
     * @Route("/connect/vk", name="connect_vk")
     */
    public function connectVkAction(Request $request)
    {
        $client = $this->get('app.oauth.vk_client_factory')->create();
        // redirect to Vk
        return ($client->authorize(['friends', 'phone', 'contacts', 'photos']));
    }

    /**
     * @Route("/connect/vk-check", name="connect_vk_check")
     */
    public function connectVkActionCheck()
    {
        // processed in VkAuthenticator
    }
}