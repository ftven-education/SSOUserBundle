<?php

namespace FTVEN\Education\SSOUserBundle\Security\Provider;

use FTVEN\Education\SSOUserBundle\Model\User;
use FTVEN\Education\SSOUserBundle\Service\ConnectorPool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProvider
 *
 * @package FTVEN\Education\SSOUserBundle\Security\Provider
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cachePool;

    /**
     * @var ConnectorPool
     */
    protected $poolConnector;

    /**
     * @var string
     */
    protected $connector;

    /**
     * @var string
     */
    protected $callback;

    /**
     * EduthequeUserProvider constructor.
     *
     * @param LoggerInterface        $logger
     * @param CacheItemPoolInterface $cachePool
     * @param ConnectorPool          $poolConnector
     */
    public function __construct(LoggerInterface $logger, CacheItemPoolInterface $cachePool, ConnectorPool $poolConnector)
    {
        $this->logger = $logger;
        $this->cachePool = $cachePool;
        $this->poolConnector = $poolConnector;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function loadUserByUsername($username)
    {
        $cacheItem = $this->getUser($username);
        /** @var User $user */
        $user = $cacheItem->get();
        if (!$user) {
            $this->logger->debug('User is not in cache');
            $user = $this->poolConnector->getConnector($this->connector)->verifyAccessToken($this->callback, $username);
            $this->logger->debug('Get new User', ['username' => $username]);
        }
        $cacheItem->set($user);
        $this->cachePool->save($cacheItem);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }

    /**
     * @param string $callback
     * @return self
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param string $connector
     */
    public function setConnector($connector)
    {
        $this->connector = $connector;
    }

    /**
     * Returns the user by given username.
     *
     * @param string $username The username
     * @return \Psr\Cache\CacheItemInterface
     */
    private function getUser($username)
    {
        $this->logger->debug('Search User in cache', ['token' => $username, 'hash_sha1' => hash("sha1", $username)]);

        return $this->cachePool->getItem(hash("sha1", $username));
    }
}