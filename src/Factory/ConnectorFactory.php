<?php

namespace FTVEN\Education\SSOUserBundle\Factory;

use FTVEN\Education\SSOUserBundle\Builder\UserBuilderInterface;
use FTVEN\Education\SSOUserBundle\Client\ClientInterface;
use FTVEN\Education\SSOUserBundle\Service\Connector;
use FTVEN\Education\SSOUserBundle\Validator\TokenValidator;
use Psr\Log\LoggerInterface;

/**
 * Class PoolConnector
 *
 * @package FTVEN\Education\SSOUserBundle\Service
 */
class ConnectorFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var TokenValidator
     */
    private $validator;

    /**
     * @var string
     */
    private $environment;

    /**
     * Connector constructor.
     *
     * @param LoggerInterface $logger
     * @param ClientInterface $client
     * @param TokenValidator  $validator
     * @param string          $environment
     */
    public function __construct(LoggerInterface $logger, ClientInterface $client, TokenValidator $validator, $environment)
    {
        $this->logger       = $logger;
        $this->client       = $client;
        $this->validator    = $validator;
        $this->environment  = $environment;
    }

    /**
     * @param array                $environment
     * @param UserBuilderInterface $userBuilder
     *
     * @return Connector
     */
    public function getService(array $environment, UserBuilderInterface $userBuilder)
    {
        $connector = new Connector($this->logger, $this->client, $this->validator, $userBuilder, $this->environment);
        foreach ($environment as $name => $urls) {
            $connector->addEnvironment($name, $urls);
        }

        return $connector;
    }
}