<?php

namespace FTVEN\Education\SSOUserBundle\Test\Security\Authenticator;

use FTVEN\Education\SSOUserBundle\Model\User;
use FTVEN\Education\SSOUserBundle\Security\Authenticator\SSOAuthenticator;
use FTVEN\Education\SSOUserBundle\Security\Provider\UserProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


/**
 * Class SSOAuthenticator
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Security\Authenticator
 */
class SSOAuthenticatorTest extends TestCase
{
    /**
     * @var LoggerInterface | MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /** @test */
    public function itMustReturnCredentials()
    {
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'log'];
        foreach ($levels as $level) {
            $this->logger->expects($this->never())->method($level);
        }
        $this->logger->expects($this->once())->method('debug');

        $query = $this->getMockBuilder(ParameterBag::class)->disableOriginalConstructor()->getMock();
        $query->expects($this->once())->method('all')->willReturn(['ticket' => 'good-edutheque-teacher', 'q' => 'sorcier']);


        /** @var Request | MockObject $request */
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $request->expects($this->exactly(3))->method('get')->willReturnCallback(function ($param, $default) {
            $this->assertTrue('service' === $param || 'ticket' === $param ||'portail' === $param);
            if ('ticket' === $param) {
                return 'token';
            }
            if ('portail' === $param) {
                return 'portail';
            }
            if ('service' === $param) {
                return 'edutheque';
            }
        });
        $request->expects($this->once())->method('getSchemeAndHttpHost')->willReturn('http://example.org');
        $request->expects($this->once())->method('getBaseUrl')->willReturn(null);
        $request->expects($this->once())->method('getPathInfo')->willReturn('/recherche');
        $request->query = $query;

        $authenticator = new SSOAuthenticator($this->logger);
        $credentials = $authenticator->getCredentials($request);
        $this->assertEquals(['token' => 'token', 'callback' => 'http://example.org/recherche?q=sorcier', 'connector' => 'edutheque'], $credentials);
    }

    /** @test */
    public function itMustReturnAUser()
    {
        /** @var User | \PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
        /** @var UserProvider | \PHPUnit_Framework_MockObject_MockObject $userProvider */
        $userProvider = $this->getMockBuilder(UserProvider::class)->disableOriginalConstructor()->getMock();
        $userProvider->expects($this->any())->method('loadUserByUsername')->willReturn($user);

        $authenticator = new SSOAuthenticator($this->logger);
        $user = $authenticator->getUser(['token' => 'good-edutheque-teacher', "connector" => 'edutheque' ,'callback' => 'http://example.org/edutheque/recherche'], $userProvider);
        $this->assertNotNull($user);
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    /** @test */
    public function itMustCheckCredentialsWithValidData()
    {
        /** @var User | \PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
        $authenticator = new SSOAuthenticator($this->logger);
        $bool = $authenticator->checkCredentials(['token' => 'good-edutheque-teacher', 'callback' => 'http://example.org/edutheque/recherche'], $user);
        $this->assertTrue($bool);
    }

    /** @test */
    public function itMustHaveAnAuthenticationSuccess()
    {
        /** @var TokenInterface | \PHPUnit_Framework_MockObject_MockObject $token */
        $token = $this->createMock(TokenInterface::class);

        /** @var Request | \PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $request->expects($this->any())->method('get')->willReturnCallback(function ($param, $default) {
            $this->assertEquals('callback', $param);
            $this->assertEquals('/', $default);

            return '/recherche/';
        });

        $authenticator = new SSOAuthenticator($this->logger);
        /** @var RedirectResponse  $redirectResponse */
        $redirectResponse = $authenticator->onAuthenticationSuccess($request, $token, 'abc');
        $this->assertNotNull($redirectResponse);
        $this->assertInstanceOf(RedirectResponse::class, $redirectResponse);
        $this->assertEquals('/recherche/', $redirectResponse->getTargetUrl());
    }

    /** @test */
    public function itMustHaveAnAuthenticationSuccessWithOutCallback()
    {
        /** @var TokenInterface | \PHPUnit_Framework_MockObject_MockObject $token */
        $token = $this->createMock(TokenInterface::class);

        /** @var Request | \PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $request->expects($this->any())->method('get')->willReturnCallback(function ($param, $default) {
            $this->assertEquals('callback', $param);
            $this->assertEquals('/', $default);

            return $default;
        });

        $authenticator = new SSOAuthenticator($this->logger);
        /** @var RedirectResponse  $redirectResponse */
        $redirectResponse = $authenticator->onAuthenticationSuccess($request, $token, 'abc');
        $this->assertNotNull($redirectResponse);
        $this->assertInstanceOf(RedirectResponse::class, $redirectResponse);
        $this->assertEquals('/', $redirectResponse->getTargetUrl());
    }

    public function testStart()
    {
        /** @var AuthenticationException | \PHPUnit_Framework_MockObject_MockObject $exception */
        $exception = $this->getMockBuilder(AuthenticationException::class)->disableOriginalConstructor()->getMock();
        /** @var Request | \PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $authenticator = new SSOAuthenticator($this->logger);
        /** @var Response $response */
        $response = $authenticator->start($request, $exception);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testSupportsRememberMe()
    {
        $authenticator = new SSOAuthenticator($this->logger);
        $this->assertFalse($authenticator->supportsRememberMe());
    }

    public function testSupports()
    {
        /** @var Request | MockObject $request */
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('get')->with('ticket')->willReturn('token');

        $authenticator = new SSOAuthenticator($this->logger);
        $this->assertTrue($authenticator->supports($request));
    }
}