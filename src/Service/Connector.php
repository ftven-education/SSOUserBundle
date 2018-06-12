<?php

namespace FTVEN\Education\SSOUserBundle\Service;

use FTVEN\Education\SSOUserBundle\Builder\UserBuilderInterface;
use FTVEN\Education\SSOUserBundle\Client\ClientInterface;
use FTVEN\Education\SSOUserBundle\Validator\TokenValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class Connector
 *
 * @package FTVEN\Education\SSOUserBundle\Service
 */
class Connector
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
     * @var UserBuilderInterface
     */
    private $userBuilder;

    /**
     * @var array
     */
    private $urls;

    /**
     * @var string
     */
    private $environment;

    /**
     * Connector constructor.
     *
     * @param LoggerInterface       $logger
     * @param ClientInterface       $client
     * @param TokenValidator        $validator
     * @param UserBuilderInterface  $userBuilder
     * @param string                $environment
     */
    public function __construct(
        LoggerInterface      $logger,
        ClientInterface      $client,
        TokenValidator       $validator,
        UserBuilderInterface $userBuilder,
        $environment
    )
    {
        $this->logger      = $logger;
        $this->client      = $client;
        $this->validator   = $validator;
        $this->urls        = [];
        $this->environment = $environment;
        $this->userBuilder = $userBuilder;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->urls['login_url'];
    }

    /**
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->urls['logout_url'];
    }

    /**
     * @return string
     */
    public function getValidateUrl()
    {
        return $this->urls['validate_url'];
    }

    /**
     * @param      $callback
     * @param null $ticket
     *
     * @return null
     * @throws \Exception
     * @throws BadRequestHttpException if ticket is null, empty or not valid
     * @throws AuthenticationException if authentication failed
     */
    public function verifyAccessToken($callback, $ticket = null)
    {
        if (null === $ticket || empty($ticket)) {
            $this->logger->error('Ticket not present in the request');
            throw new BadRequestHttpException('Ticket not present in the request');
        }
        $xmlContent = $this->client->get($this->getValidateUrl(), ['ticket' => $ticket, 'service' => urlencode($callback)]);
        $this->validator->handledData($xmlContent);
        if ($this->validator->isValid()) {
            return $this->userBuilder->buildUser($this->validator->getData(), $ticket);
        }

        throw new AuthenticationException('Token is invalid');
    }

    /**
     * @param array $url
     *
     * @return $this
     */
    public function setUrl(array $url)
    {
        $this->urls = $url;

        return $this;
    }
}