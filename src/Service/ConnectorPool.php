<?php

namespace FTVEN\Education\SSOUserBundle\Service;

use FTVEN\Education\SSOUserBundle\Service\Connector;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class PoolConnector
 *
 * @package FTVEN\Education\SSOUserBundle\Service
 */
class ConnectorPool
{
    /**
     * @var Connector[]
     */
    private $connectors;

    public function __construct()
    {
        $this->connectors = [];
    }

    /**
     * @param string    $name
     * @param Connector $connector
     */
    public function addConnector($name, Connector $connector)
    {
        $this->connectors[$name] = $connector;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasConnector($name)
    {
        return isset($this->connectors[$name]);
    }

    /**
     * @param string $name
     * @throws BadRequestHttpException
     *
     * @return Connector
     */
    public function getConnector($name)
    {
        if (!$this->hasConnector($name)) {
            throw new BadRequestHttpException(sprintf("The connector [%s] don't exist", $name));
        }

        return $this->connectors[$name];
    }
}