<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 31.08.2017
 * Time: 16:37
 */

namespace AppBundle\Service\OauthClientAuthentication\VkClient;

use AppBundle\Service\OauthClientAuthentication\Contracts\OauthClient;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class OauthVkClient
 * @package AppBundle\Service\OauthClientAuthentication\VkClient
 */
class OauthVkClient extends OauthClient
{
    private $instance;

    private $caCertificate = '/RootCertificates/cacert.pem';

    private $authorizeUrl = 'https://oauth.vk.com/authorize?';

    private $getAccessTokenUrl = 'https://oauth.vk.com/access_token?';

    private $apiRequestUrl = 'https://api.vk.com/method/';

    private $clientId = '6167233';

    private $secret = 'ptpIpBP7i4QVPQqp80um';

    private $redirectUri;

    public function __construct(Container $container, Request $request)
    {
        $this->redirectUri = $request->getSchemeAndHttpHost().'/connect/vk-check';

        $this->instance = new \GuzzleHttp\Client([
            'verify' => $container->get('kernel')->getRootDir().$this->caCertificate,
        ]);
    }

    public function authorize(array $scope = [])
    {
        if ($scope) {
            $scopeString = ','.implode(',', $scope);
        } else {
            $scopeString = '';
        }

        $url = $this->authorizeUrl
            .'client_id='.$this->clientId
            .'&display=page&redirect_uri='.$this->redirectUri
            .'&scope=email'.$scopeString
            .'&response_type=code&v=5.68';

        return new RedirectResponse($url);
    }

    public function getAccessToken($code = '')
    {
        $url = $this->getAccessTokenUrl
            .'client_id='.$this->clientId
            .'&client_secret='.$this->secret
            .'&redirect_uri='.$this->redirectUri
            .'&code='.$code;

        $response = $this
            ->instance
            ->get($url)
            ->getBody()
            ->getContents();

        return json_decode($response);
    }

    public function apiRequest($methodName = '', $userData, array $parameters = [])
    {
        $url = $this->apiRequestUrl.$methodName.'?user_ids='.$userData->user_id.'&access_token='.$userData->access_token;

        if ($parameters) {
            $parameters = implode(',', $parameters);
            $url .= '&fields='.$parameters;
        }

        $response = $this
            ->instance
            ->get($url)
            ->getBody()
            ->getContents();

        $response = json_decode($response);

        return $response->response[0];
    }
}