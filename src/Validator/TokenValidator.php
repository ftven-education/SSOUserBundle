<?php

namespace FTVEN\Education\SSOUserBundle\Validator;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class TokenValidator
 *
 * @package FTVEN\Education\SSOUserBundle\Validator
 */
class TokenValidator
{
    const CAS_NS = "http://www.yale.edu/tp/cas";

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    private $valid;

    /**
     * @var null|\DOMDocument
     */
    private $data;

    /**
     * TokenValidator constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->valid  = true;
        $this->data   =  null;
    }

    /**
     * @param string $xmlContent
     */
    public function handledData($xmlContent)
    {
        $this->logger->debug("Ticket's Verification", ['xmlContent' => $xmlContent]);
        try {
            $dom = new \DOMDocument("1.0");
            $dom->loadXML($xmlContent);
            $this->checkResponse($dom);
            $this->checkAuthification($dom);
            $this->data = $dom;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['content' => $xmlContent]);
            $this->valid = false;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DOMDocument $dom
     */
    protected function  checkResponse(\DOMDocument $dom)
    {
        if ($dom->documentElement->nodeName != 'cas:serviceResponse') {
            $this->logger->error('cas:serviceResponse not found', [
                'xml' => trim($dom->documentElement->nodeValue),
            ]);

            throw new AuthenticationException("cas:serviceResponse not found in Response");
        }
    }

    protected function checkAuthification(\DOMDocument $dom)
    {
        $successElement = $dom->documentElement->getElementsByTagNameNS(self::CAS_NS, 'authenticationSuccess');

        if ($successElement->length == 0) {
            $this->logger->error('cas:authenticationSuccess not found', [
                'xml' => trim($dom->documentElement->nodeValue)
            ]);

            throw new AuthenticationException("cas:authenticationSuccess not found in response");
        }
    }
}