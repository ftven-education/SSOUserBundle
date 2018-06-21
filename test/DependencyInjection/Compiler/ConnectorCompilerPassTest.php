<?php

namespace DependencyInjection\Compiler;

use FTVEN\Education\SSOUserBundle\DependencyInjection\Compiler\ConnectorCompilerPass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class ConnectorCompilerPassTest
 *
 * @package DependencyInjection\Compiler
 */
class ConnectorCompilerPassTest extends TestCase
{
    /**
     * @var ContainerBuilder | MockObject
     */
    private $container;

    /**
     * @var Definition | MockObject
     */
    private $factory;

    public function setUp()
    {
        $this->container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $this->factory = $this->getMockBuilder(Definition::class)->disableOriginalConstructor()->getMock();
    }

    /** @test */
    public function itMustNotAddConnector()
    {
        $this->factory->expects($this->never())->method('addMethodCall');

        $this->container->expects($this->once())->method('getExtensionConfig')->willReturn([['connectors' => []]]);
        $this->container->expects($this->once())->method('getDefinition')->willReturn($this->factory);
        $this->container->expects($this->never())->method('hasDefinition');
        $this->container->expects($this->never())->method('register');

        $compilerPass = new ConnectorCompilerPass();
        $compilerPass->process($this->container);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage You must implement the service id [sso_user.build.edutheque]
     */
    public function itMustThrowExceptionBecauseBuilderNotFound()
    {
        $configs = [
            [
                'connectors' => [
                    'edutheque' => [
                        'login_url' => 'http://example.org/login_url',
                        'logout_url' => 'http://example.org/logout_url',
                        'validate_url' => 'http://example.org/validate_url',
                    ]
                ]
            ]
        ];

        $this->factory->expects($this->never())->method('addMethodCall');

        $this->container->expects($this->once())->method('getExtensionConfig')->willReturn($configs);
        $this->container->expects($this->once())->method('getDefinition')->willReturn($this->factory);
        $this->container->expects($this->once())->method('hasDefinition')->willReturn(false);
        $this->container->expects($this->never())->method('register');

        $compilerPass = new ConnectorCompilerPass();
        $compilerPass->process($this->container);
    }

    /** @test */
    public function itMustAddConnector()
    {
        $configs = [
            [
                'connectors' => [
                    'edutheque' => [
                        'login_url' => 'http://example.org/login_url',
                        'logout_url' => 'http://example.org/logout_url',
                        'validate_url' => 'http://example.org/validate_url',
                    ]
                ]
            ]
        ];

        $definition = $this->getMockBuilder(Definition::class)->disableOriginalConstructor()->getMock();
        $definition->expects($this->once())->method('setFactory')->willReturn($definition);
        $definition->expects($this->exactly(2))->method('addArgument')->willReturn($definition);

        $this->factory->expects($this->once())->method('addMethodCall');

        $this->container->expects($this->once())->method('getExtensionConfig')->willReturn($configs);
        $this->container->expects($this->exactly(2))->method('getDefinition')->willReturnCallback(function ($service) {
            $this->assertTrue($service === 'sso_user.connector.pool' || $service === 'sso_user.build.edutheque');
            if ($service === 'sso_user.connector.pool') {
                return $this->factory;
            } else {
                return $this->getMockBuilder(Definition::class)->disableOriginalConstructor()->getMock();
            }
        });
        $this->container->expects($this->once())->method('hasDefinition')->willReturn(true);
        $this->container->expects($this->once())->method('register')->willReturn($definition);

        $compilerPass = new ConnectorCompilerPass();
        $compilerPass->process($this->container);
    }
}