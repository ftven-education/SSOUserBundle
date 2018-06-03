<?php

namespace FTVEN\Education\SSOUserBundle\Test\Service;

use FTVEN\Education\SSOUserBundle\Service\Connector;
use FTVEN\Education\SSOUserBundle\Service\ConnectorPool;
use PHPUnit\Framework\TestCase;

/**
 * Class ConnectorPoolTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Service
 */
class ConnectorPoolTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage The connector [test] don't exist
     */
    public function itMustThrowExceptionBecauseTheConnectorNotExist()
    {
        $pool = new ConnectorPool();
        $this->assertInstanceOf(Connector::class, $pool->getConnector('test'));
    }

    /** @test */
    public function itMustAddAConnector()
    {
        /** @var Connector $connector */
        $connector = $this->createMock(Connector::class);
        $pool = new ConnectorPool();
        $pool->addConnector('test', $connector);
        $this->assertInstanceOf(Connector::class, $pool->getConnector('test'));
    }
}