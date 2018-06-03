<?php

namespace FTVEN\Education\SSOUserBundle\Client;

/**
 * Class DefaultClient
 *
 * @package FTVEN\Education\SSOUserBundle\Client
 */
class DefaultClient implements ClientInterface
{
    /**
     * @param string $uri
     * @param array  $params
     * @return string
     */
    public function get($uri, array $params)
    {
        $uri = sprintf("%s?%s", $uri, implode('&', array_map(function ($key, $value) {
            return sprintf('%s=%s', $key, $value);
        }, array_keys($params), $params)));

        return file_get_contents($uri);
    }
}