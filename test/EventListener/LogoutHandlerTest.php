<?php

namespace FTVEN\Education\SSOUserBundle\Test\EventListener;

use FTVEN\Education\SSOUserBundle\EventListener\LogoutHandler;
use FTVEN\Education\SSOUserBundle\Service\Connector;
use FTVEN\Education\SSOUserBundle\Service\ConnectorPool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class LogoutHandlerTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\EventListener
 */
class LogoutHandlerTest extends TestCase
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
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var ConnectorPool
     */
    protected $pool;

    public function setUp()
    {
        $this->httpUtils = $this->getMockBuilder(HttpUtils::class)->disableOriginalConstructor()->getMock();
        $this->httpUtils->expects($this->once())->method('createRedirectResponse')->willReturnCallback(function ($request, $redirectUrl, $status) {
            return new RedirectResponse('http://example.org?service=toto', $status);
        });
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $connector = $this->getMockBuilder(Connector::class)->disableOriginalConstructor()->getMock();
        $connector->expects($this->any())->method('getLogoutUrl')->willReturn("");
        $this->pool = $this->getMockBuilder(ConnectorPool::class)->disableOriginalConstructor()->getMock();
        $this->pool->expects($this->once())->method('getConnector')->willReturn($connector);
        $this->router = $this->createMock(UrlGeneratorInterface::class);;
    }

    /** @test */
    public function itMustLogoutSuccessful()
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('invalidate');

        $headerBag = $this->getMockBuilder(HeaderBag::class)->disableOriginalConstructor()->getMock();
        $headerBag->expects($this->once())->method('get')->willReturn('toto');

        /** @var Request | \PHPUnit_Framework_MockObject_MockObject $request */
        $request =  $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('getSession')->willReturn($session);
        $request->headers = $headerBag;

        $listener = new LogoutHandler($this->httpUtils, $this->tokenStorage,$this->pool);
        $response = $listener->onLogoutSuccess($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://example.org?service=toto', $response->getTargetUrl());
    }
}