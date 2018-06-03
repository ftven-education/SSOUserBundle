<?php

namespace FTVEN\Education\SSOUserBundle\Test\Client;

use FTVEN\Education\SSOUserBundle\Client\DefaultClient;
use PHPUnit\Framework\TestCase;

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
        $client = new DefaultClient();
        $content = json_decode($client->get("https://jsonplaceholder.typicode.com/posts", ['userId' => 1]), true);
        $this->assertTrue(is_array($content));
        $this->assertNotEmpty($content);
    }
}