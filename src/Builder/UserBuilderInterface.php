<?php

namespace FTVEN\Education\SSOUserBundle\Builder;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface UserBuilderInterface
 *
 * @package FTVEN\Education\SSOUserBundle\Builder
 */
interface UserBuilderInterface
{
    /**
     * @param \DOMDocument $document
     * @param              $ticket
     *
     * @return UserInterface
     */
    public function buildUser(\DOMDocument $document, $ticket);
}