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
    private $urlsByEnvironments;

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
        $this->logger             = $logger;
        $this->client             = $client;
        $this->validator          = $validator;
        $this->urlsByEnvironments = [];
        $this->environment        = $environment;
        $this->userBuilder        = $userBuilder;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        $urls = $this->getUrls();

        return $urls['login_url'];
    }

    /**
     * @return string
     */
    public function getLogoutUrl()
    {
        $urls = $this->getUrls();

        return $urls['logout_url'];
    }

    /**
     * @return string
     */
    public function getValidateUrl()
    {
        $urls = $this->getUrls();

        return $urls['validate_url'];
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
     * @param string $name
     * @param array  $value
     *
     * @return $this
     */
    public function addEnvironment($name, array $value)
    {
        $this->urlsByEnvironments[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    private function getUrls()
    {
        foreach ($this->urlsByEnvironments as $urlsByEnvironment) {
            if (in_array($this->environment, $urlsByEnvironment['for_environments'])) {
                return $urlsByEnvironment;
            }
        }

        $this->logger->critical("Environment doesn't exist in auth's connector", [
            'environment' => $this->environment,
            'connector'   => 'gar',
            'method'      => 'getValidateUrl',
        ]);
        throw new \InvalidArgumentException(sprintf("The environment [%s] does not exist", $this->environment));
    }
}