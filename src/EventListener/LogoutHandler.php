<?php

namespace FTVEN\Education\SSOUserBundle\EventListener;

use FTVEN\Education\SSOUserBundle\Service\ConnectorPool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutHandler
 *
 * @package FTVEN\Education\SSOUserBundle\EventListener
 */
class LogoutHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var HttpUtils
     */
    protected $httpUtils;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var ConnectorPool
     */
    protected $pool;

    /**
     * LogoutHandler constructor.
     *
     * @param HttpUtils             $httpUtils
     * @param TokenStorageInterface $tokenStorage
     * @param ConnectorPool         $pool
     */
    public function __construct(HttpUtils $httpUtils, TokenStorageInterface $tokenStorage, ConnectorPool $pool)
    {
        $this->httpUtils = $httpUtils;
        $this->tokenStorage = $tokenStorage;
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $connector = $this->pool->getConnector($request->get('service'));
        $connector->getLogoutUrl();
        $redirectUrl = $connector->getLogoutUrl() . '?service=' . $request->headers->get('referer');

        $this->tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        return $this->httpUtils->createRedirectResponse($request, $redirectUrl);
    }
}