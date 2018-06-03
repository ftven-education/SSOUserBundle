<?php

namespace FTVEN\Education\SSOUserBundle\Client;

/**
 * Interface ClientInterface
 *
 * @package FTVEN\Education\SSOUserBundle\Client
 */
interface ClientInterface
{
    /**
     * @param string $uri
     * @param array  $params
     * @return string
     * @throws \Exception
     */
    public function get($uri, array $params);
}