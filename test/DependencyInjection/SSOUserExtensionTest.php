<?php

namespace FTVEN\Education\SSOUserBundle\Test\DependencyInjection;

use FTVEN\Education\SSOUserBundle\Builder\UserBuilderInterface;
use FTVEN\Education\SSOUserBundle\DependencyInjection\SSOUserExtension;
use FTVEN\Education\SSOUserBundle\Service\ConnectorPool;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * Class SSOUserExtensionTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\DependencyInjection
 */
class SSOUserExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new SSOUserExtension(),
        );
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage You must implement the service id [sso_user.build.foo]
     */
    public function itMustThrowAnExceptionBecauseBuilderIsNotRegister()
    {
        $config = [
            'connectors' => [
                'foo' => [
                    "prod" => [
                        'login_url' => 'https://cas.edutheque.cndp.fr/login',
                        'logout_url' => 'https://cas.edutheque.cndp.fr/logout',
                        'validate_url' => 'https://cas.edutheque.cndp.fr/serviceValidate',
                        'for_environments' => ['dev', 'test', 'integ', 'preprod', 'prod'],
                    ],
                ],
            ],
        ];

        $this->container->register('sso_user.connector.pool', ConnectorPool::class);

        $this->load($config);
    }

    /** @test */
    public function itMustAddConnectorService()
    {
        $config = [
            'connectors' => [
                'foo' => [
                    "prod" => [
                        'login_url' => 'https://cas.edutheque.cndp.fr/login',
                        'logout_url' => 'https://cas.edutheque.cndp.fr/logout',
                        'validate_url' => 'https://cas.edutheque.cndp.fr/serviceValidate',
                        'for_environments' => ['dev', 'test', 'integ', 'preprod', 'prod'],
                    ],
                ],
            ],
        ];

        $this->container->register('sso_user.connector.pool', ConnectorPool::class);
        $this->container->register('sso_user.build.foo', UserBuilderInterface::class);

        $this->load($config);

        $this->assertContainerBuilderHasService('sso_user.build.foo');
        $this->assertContainerBuilderHasService('sso_user.build.edutheque');
        $this->assertContainerBuilderHasService('sso_user.client.default');
        $this->assertContainerBuilderHasService('sso_user.security.authenticator.token');
        $this->assertContainerBuilderHasService('sso_user.security.provider.user');
        $this->assertContainerBuilderHasService('sso_user.logout_handler');
        $this->assertContainerBuilderHasService('sso_user.factory.connector');
        $this->assertContainerBuilderHasService('sso_user.connector.pool');
        $this->assertContainerBuilderHasService('sso_user.validator.token');
    }
}