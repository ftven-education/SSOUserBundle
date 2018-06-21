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

    /** @test */
    public function itMustAddConnectorService()
    {
        $config = [
            'connectors' => [
                'foo' => [
                    'login_url' => 'https://cas.edutheque.cndp.fr/login',
                    'logout_url' => 'https://cas.edutheque.cndp.fr/logout',
                    'validate_url' => 'https://cas.edutheque.cndp.fr/serviceValidate',
                ],
            ],
        ];
        $this->container->setParameter('kernel.environment', 'prod');
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

    /** @test */
    public function itMustAddConnectorServiceWithTestEnvironment()
    {
        $config = [
            'connectors' => [
                'foo' => [
                    'login_url' => 'https://cas.edutheque.cndp.fr/login',
                    'logout_url' => 'https://cas.edutheque.cndp.fr/logout',
                    'validate_url' => 'https://cas.edutheque.cndp.fr/serviceValidate',
                ],
            ],
        ];
        $this->container->setParameter('kernel.environment', 'test');
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