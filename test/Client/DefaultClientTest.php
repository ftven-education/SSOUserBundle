<?php

namespace FTVEN\Education\SSOUserBundle\Test\Client;

use FTVEN\Education\SSOUserBundle\Client\DefaultClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class DefaultClientTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Client
 */
class DefaultClientTest extends TestCase
{
    /** @test */
    public function itMustReturnSomeData()
    {
        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'log'];
        foreach ($levels as $level) {
            $logger->expects($this->never())->method($level);
        }
        $logger->expects($this->once())->method('debug');

        $client = new DefaultClient($logger);
        $content = json_decode($client->get("https://jsonplaceholder.typicode.com/posts", ['userId' => 1]), true);
        $this->assertTrue(is_array($content));
        $this->assertNotEmpty($content);
    }
}