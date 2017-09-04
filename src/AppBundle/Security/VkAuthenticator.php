<?php
/**
 * Created by PhpStorm.
 * User: a.abelyan
 * Date: 30.08.2017
 * Time: 15:03
 */

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Service\GuzzleClient;
use AppBundle\Service\MailHelper\Contracts\MailHelper;
use AppBundle\Service\MailHelper\Contracts\MailHelperFactory;
use AppBundle\Service\OauthClientAuthentication\Contracts\OauthClientFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;


class VkAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var
     */
    private $client;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var
     */
    private $mailHelper;

    /**
     * @var Router
     */
    private $router;

    /**
     * VkAuthenticator constructor.
     * @param OauthClientFactory $clientFactory
     * @param Container $container
     * @param EntityManager $em
     * @param MailHelperFactory $mailHelperFactory
     * @param Router $router
     */
    public function __construct(OauthClientFactory $clientFactory, Container $container, EntityManager $em, MailHelperFactory $mailHelperFactory, Router $router)
    {
        $this->client = $clientFactory->create();
        $this->container = $container;
        $this->em = $em;
        $this->mailHelper = $mailHelperFactory->create();
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function getCredentials(Request $request)
    {
        $isVkLoginSubmit = $request->getPathInfo() == '/connect/vk-check' && $request->isMethod('GET');

        if (!$isVkLoginSubmit) {
            return;
        }

        if ($code = $request->query->get('code')) {
            return $code;
        }

        throw CustomAuthenticationException::createWithSafeMessage(
            'There was an error getting access from Vk. Please try again.'
        );
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $userData = $this->client->getAccessToken($credentials);

        $user = $this->client->apiRequest('users.get', $userData);

        $existingUser = $this->em->getRepository('AppBundle:User')
            ->findOneBy([
                'vkId' => $userData->user_id,
            ]);

        if (!$existingUser) {
            $newUser = new User();

            // create password
            $plainPassword = uniqid(uniqid());

            $newUser->setEmail($userData->email);
            $newUser->setFirstName($user->first_name);
            $newUser->setLastName($user->last_name);
            $newUser->setPlainTextPassword($plainPassword);
            $newUser->setRoles(['ROLE_USER']);
            $newUser->setVkId($user->uid);

            $this->em->persist($newUser);
            $this->em->flush();

            $this->mailHelper->createMessage($plainPassword, $userData->email)
                ->send();

            return $newUser;
        }

        return $existingUser;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

        throw new $exception('Auth failure');
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('homepage'));
    }

    /**
     *
     */
    public function supportsRememberMe()
    {
        // TODO: Implement supportsRememberMe() method.
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        // TODO: Implement start() method.
    }
}