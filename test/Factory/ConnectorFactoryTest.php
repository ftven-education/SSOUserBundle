<?php

namespace FTVEN\Education\SSOUserBundle\Test\Factory;

use FTVEN\Education\SSOUserBundle\Builder\UserBuilderInterface;
use FTVEN\Education\SSOUserBundle\Client\ClientInterface;
use FTVEN\Education\SSOUserBundle\Factory\ConnectorFactory;
use FTVEN\Education\SSOUserBundle\Service\Connector;
use FTVEN\Education\SSOUserBundle\Validator\TokenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ConnectorFactoryTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Factory
 */
class ConnectorFactoryTest extends TestCase
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

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->client = $this->createMock(ClientInterface::class);
        $this->validator = $this->getMockBuilder(TokenValidator::class)->disableOriginalConstructor()->getMock();
    }

    /** @test */
    public function itMustCreateConnectorWithOutEnvironment()
    {
        /** @var UserBuilderInterface | MockObject $userBuilder */
        $userBuilder = $this->createMock(UserBuilderInterface::class);

        $factory = new ConnectorFactory($this->logger, $this->client, $this->validator, 'prod');
        $this->assertInstanceOf(Connector::class, $factory->getService([], $userBuilder));
    }

    /** @test */
    public function itMustCreateConnectorWithEnvironments()
    {
        $environments = [
            "prod" => [
                'login_url' => 'https://example.org/login',
                'logout_url' => 'https://example.org/logout',
                'validate_url' => 'https://example.org/serviceValidate',
                'for_environments' => ['dev', 'test', 'preprod', 'prod'],
            ],
        ];
        /** @var UserBuilderInterface | MockObject $userBuilder */
        $userBuilder = $this->createMock(UserBuilderInterface::class);

        $factory = new ConnectorFactory($this->logger, $this->client, $this->validator, 'prod');
        $this->assertInstanceOf(Connector::class, $factory->getService($environments, $userBuilder));
    }
}