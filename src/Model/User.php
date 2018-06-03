<?php

namespace FTVEN\Education\SSOUserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @package FTVEN\Education\SSOUserBundle\Model
 */
class User implements UserInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $connector;

    /**
     * @var array
     */
    private $stages;

    /**
     * @var array
     */
    private $courses;

    public function __construct()
    {
        $this->stages = [];
        $this->courses = [];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * @codeCoverageIgnore
     * @return string|void
     */
    public function getPassword()
    {
    }

    /**
     * @codeCoverageIgnore
     * @return string|void
     */
    public function getSalt()
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @param string $connector
     */
    public function setConnector($connector)
    {
        $this->connector = $connector;
    }

    /**
     * @return array
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * @return array
     */
    public function getCourses()
    {
        return $this->courses;
    }

    public function setStages(array $stages)
    {
        $this->stages = $stages;
    }

    public function setCourses($courses)
    {
        $this->courses= $courses;
    }
}