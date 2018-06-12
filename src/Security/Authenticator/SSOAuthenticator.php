<?php

namespace FTVEN\Education\SSOUserBundle\Security\Authenticator;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SSOAuthenticator
 *
 * @package FTVEN\Education\SSOUserBundle\Security\Authenticator
 */
class SSOAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TokenAuthenticator constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     * ---
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $token = $request->get('ticket');
        $service = $request->get('service', $request->get('portail'));
        $callback = $this->getCallback($request);
        $this->logger->debug('Ticket is in request', [
            'token' => $token,
            'callback' => $callback,
            'connector' => $service
        ]);

        return ['token' => $token, 'callback' => $callback, 'connector' => $service];
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];
        $userProvider->setCallback($credentials['callback']);
        $userProvider->setConnector($credentials['connector']);

        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $userProvider->loadUserByUsername($apiKey);
    }

    /**
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->logger->info('User is autentication');

        return new RedirectResponse($request->get('callback', '/'));
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    /**
     * Called when authentication is needed, but it's not sent
     * ---
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     * @throws AuthenticationException if authentication is needed
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('Authentication Required', Response::HTTP_FORBIDDEN);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getCallback(Request $request)
    {
        $uri = $request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo();
        $query  = $request->query->all();
        unset($query['ticket']);
        unset($query['service']);
        $request->query->remove('ticket');
        $request->query->remove('service');
        $params = implode('&', array_map(function ($key, $value) {
            return sprintf('%s=%s', $key, $value);
        }, array_keys($query), $query));

        return empty($params) ? $uri : sprintf("%s?%s", $uri, $params);
    }

    public function supports(Request $request)
    {
        $token = $request->get('ticket');

        return $token === null ? false : true;
    }
}