<?php

namespace FTVEN\Education\SSOUserBundle\Test\Service;

use FTVEN\Education\SSOUserBundle\Builder\UserBuilderInterface;
use FTVEN\Education\SSOUserBundle\Client\ClientInterface;
use FTVEN\Education\SSOUserBundle\Model\User;
use FTVEN\Education\SSOUserBundle\Service\Connector;
use FTVEN\Education\SSOUserBundle\Validator\TokenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ConnectorTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Service
 */
class ConnectorTest extends TestCase
{
    /**
     * @var LoggerInterface | MockObject
     */
    private $logger;

    /**
     * @var ClientInterface | MockObject
     */
    private $client;

    /**
     * @var TokenValidator | MockObject
     */
    private $validator;

    /**
     * @var UserBuilderInterface | MockObject
     */
    private $userBuilder;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->client = $this->createMock(ClientInterface::class);
        $this->validator = $this->getMockBuilder(TokenValidator::class)->disableOriginalConstructor()->getMock();
        $this->userBuilder = $this->createMock(UserBuilderInterface::class);
    }

    /** @test */
    public function itMustReturnLoginUrl()
    {
        $urls = [
            'login_url' => 'https://example.org/login',
            'logout_url' => 'https://example.org/logout',
            'validate_url' => 'https://example.org/serviceValidate',
        ];
        $this->client->expects($this->never())->method('get');
        $this->validator->expects($this->never())->method('handledData');
        $this->validator->expects($this->never())->method('isValid');
        $this->userBuilder->expects($this->never())->method('buildUser');
        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->setUrl($urls);
        $this->assertEquals($urls['login_url'], $connector->getLoginUrl());
    }

    /** @test */
    public function itMustReturnLogoutUrl()
    {
        $urls = [
            'login_url' => 'https://example.org/login',
            'logout_url' => 'https://example.org/logout',
            'validate_url' => 'https://example.org/serviceValidate',
        ];
        $this->client->expects($this->never())->method('get');
        $this->validator->expects($this->never())->method('handledData');
        $this->validator->expects($this->never())->method('isValid');
        $this->userBuilder->expects($this->never())->method('buildUser');
        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->setUrl($urls);
        $this->assertEquals($urls['logout_url'], $connector->getLogoutUrl());
    }

    /** @test */
    public function itMustReturnValidateUrl()
    {
        $urls = [
            'login_url' => 'https://example.org/login',
            'logout_url' => 'https://example.org/logout',
            'validate_url' => 'https://example.org/serviceValidate',
        ];
        $this->client->expects($this->never())->method('get');
        $this->validator->expects($this->never())->method('handledData');
        $this->validator->expects($this->never())->method('isValid');
        $this->userBuilder->expects($this->never())->method('buildUser');
        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->setUrl($urls);
        $this->assertEquals($urls['validate_url'], $connector->getValidateUrl());
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Ticket not present in the request
     *
     * @throws \Exception
     */
    public function itMustThrowExceptionBecauseTokenIsNotDefined()
    {
        $this->client->expects($this->never())->method('get');
        $this->validator->expects($this->never())->method('handledData');
        $this->validator->expects($this->never())->method('isValid');
        $this->userBuilder->expects($this->never())->method('buildUser');

        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->verifyAccessToken("http://example.org");
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Ticket not present in the request
     *
     * @throws \Exception
     */
    public function itMustThrowExceptionBecauseTokenIsEmpty()
    {
        $this->client->expects($this->never())->method('get');
        $this->validator->expects($this->never())->method('handledData');
        $this->validator->expects($this->never())->method('isValid');
        $this->userBuilder->expects($this->never())->method('buildUser');

        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->verifyAccessToken("http://example.org", '');
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @expectedExceptionMessage Token is invalid
     *
     * @throws \Exception
     */
    public function itMustThrowExceptionBecauseTokenIsInvalid()
    {
        $urls = [
            'login_url' => 'https://example.org/login',
            'logout_url' => 'https://example.org/logout',
            'validate_url' => 'https://example.org/serviceValidate',
        ];

        $user = $this->createMock(UserInterface::class);

        $this->client->expects($this->once())->method('get')->willReturn('xmlContent');
        $this->validator->expects($this->once())->method('handledData');
        $this->validator->expects($this->once())->method('isValid')->willReturn(false);
        $this->validator->expects($this->never())->method('getData');
        $this->userBuilder->expects($this->never())->method('buildUser');

        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->setUrl($urls);
        $this->assertInstanceOf(UserInterface::class, $connector->verifyAccessToken("http://example.org", 'token'));
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function itMustReturnAUser()
    {
        $urls = [
            'login_url' => 'https://example.org/login',
            'logout_url' => 'https://example.org/logout',
            'validate_url' => 'https://example.org/serviceValidate',
        ];

        $user = $this->createMock(UserInterface::class);

        $this->client->expects($this->once())->method('get')->willReturn('xmlContent');
        $this->validator->expects($this->once())->method('handledData');
        $this->validator->expects($this->once())->method('isValid')->willReturn(true);
        $this->validator->expects($this->once())->method('getData')->willReturn(new \DOMDocument());
        $this->userBuilder->expects($this->once())->method('buildUser')->willReturn($user);

        $connector = new Connector($this->logger, $this->client, $this->validator, $this->userBuilder, 'prod');
        $connector->setUrl($urls);
        $this->assertInstanceOf(UserInterface::class, $connector->verifyAccessToken("http://example.org", 'token'));
    }
}