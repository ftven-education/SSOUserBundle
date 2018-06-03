<?php

namespace FTVEN\Education\SSOUserBundle\Test\Security\Provider;

use FTVEN\Education\SSOUserBundle\Model\User;
use FTVEN\Education\SSOUserBundle\Security\Provider\UserProvider;
use FTVEN\Education\SSOUserBundle\Service\Connector;
use FTVEN\Education\SSOUserBundle\Service\ConnectorPool;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class UserProviderTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Security\Provider
 */
class UserProviderTest extends TestCase
{
    /**
     * @var LoggerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var CacheItemPoolInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $cachePool;

    /**
     * @var ConnectorPool | \PHPUnit_Framework_MockObject_MockObject
     */
    private $poolConnector;

    public function setUp()
    {
        $this->logger = new NullLogger();
        $this->cachePool = $this->createMock(CacheItemPoolInterface::class);
        $this->poolConnector = $this->getMockBuilder(ConnectorPool::class)->disableOriginalConstructor()->getMock();
    }

    /** @test */
    public function itMustLoadUserByUsernameWithCache()
    {
        $connector = $this->getMockBuilder(Connector::class)->disableOriginalConstructor()->getMock();
        $connector->expects($this->never())->method('verifyAccessToken');
        $this->cachePool->expects($this->once())->method('save');
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())->method('get')->willReturn($user);
        $this->cachePool->expects($this->once())->method('getItem')->willReturn($cacheItem);

        $provider = new UserProvider($this->logger, $this->cachePool, $this->poolConnector);
        $user = $provider->loadUserByUsername('John Doe');
        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function itMustLoadUserByUsernameWithOutCache()
    {
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
        $connector = $this->createMock(Connector::class);
        $connector->expects($this->once())->method('verifyAccessToken')->willReturn($user);
        $this->cachePool->expects($this->once())->method('save');
        $this->poolConnector->expects($this->once())->method('getConnector')->willReturn($connector);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())->method('get')->willReturn(null);
        $cacheItem->expects($this->once())->method('set');
        $this->cachePool->expects($this->once())->method('getItem')->willReturn($cacheItem);

        $provider = new UserProvider($this->logger, $this->cachePool, $this->poolConnector);
        $provider->setCallback('http//example.org');
        $provider->setConnector('edutheque');
        $user = $provider->loadUserByUsername('John Doe');
        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function itMustRefreshUser()
    {
        /** @var User $user */
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $provider = new UserProvider($this->logger, $this->cachePool, $this->poolConnector);
        $nUser = $provider->refreshUser($user);
        $this->assertInstanceOf(User::class, $nUser);
    }

    /**
     * @test
     * @dataProvider dataProviderSupportsClass
     *
     * @param string $class
     * @param bool   $isValid
     */
    public function itMustSupportsClass($class, $isValid)
    {
        $provider = new UserProvider($this->logger, $this->cachePool, $this->poolConnector);
        $this->assertEquals($isValid, $provider->supportsClass($class));
    }

    /**
     * @return array
     */
    public function dataProviderSupportsClass()
    {
        return [
            [
                User::class,
                true
            ],
            [
                \stdClass::class,
                false
            ]
        ];
    }
}