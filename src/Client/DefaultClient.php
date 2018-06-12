<?php

namespace FTVEN\Education\SSOUserBundle\Client;

use Psr\Log\LoggerInterface;

/**
 * Class DefaultClient
 *
 * @package FTVEN\Education\SSOUserBundle\Client
 */
class DefaultClient implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DefaultClient constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
        $this->logger->debug('Call url', ['url' => $uri]);

        return file_get_contents($uri);
    }
}