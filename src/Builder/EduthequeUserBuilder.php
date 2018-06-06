<?php

namespace FTVEN\Education\SSOUserBundle\Builder;

use FTVEN\Education\SSOUserBundle\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class EduthequeUserBuilder
 *
 * @package FTVEN\Education\SSOUserBundle\Builder
 */
class EduthequeUserBuilder implements UserBuilderInterface
{
    const CAS_NS                = "http://www.yale.edu/tp/cas";
    const NODE_LASTNAME         = 'nom';
    const NODE_FIRSTNAME        = 'prenom';
    const NODE_EMAIL            = 'email';
    const NODE_FUNCTION         = 'fonction';
    const NODE_STUDENT_USERNAME = 'user';
    const NODE_USER_ID          = "idUser";
    const USER_TEACHER          = "1";
    const USER_STUDENT          = "2";

    /**
     * @param \DOMDocument $document
     * @param              $ticket
     *
     * @return UserInterface
     */
    public function buildUser(\DOMDocument $document, $ticket)
    {
        $infos = $this->parseInfoUser($document);

        $user = new User();
        $user->setUsername($ticket);
        $user->setApiKey($ticket);
        $user->setIdConnector($infos['idConnector']);
        $user->addRole('ROLE_USER');
        $user->setConnector('edutheque');
        $user->setStages($infos['stages']);
        $user->setCourses($infos['courses']);
        $user->setFirstName($infos['firstName']);
        $user->setLastName($infos['lastName']);
        $user->setEmail($infos['email']);
        foreach ($this->getRoles($infos) as $role) {
            $user->addRole($role);
        }

        return $user;
    }

    /**
     * @param \DOMDocument $document
     *
     * @return array
     */
    private function parseInfoUser(\DOMDocument $document)
    {
        $infos = [
            'function' => $document->getElementsByTagName(self::NODE_FUNCTION)->item(0)->nodeValue,
            'idConnector' => $document->getElementsByTagName(self::NODE_USER_ID)->item(0)->nodeValue,
            'email' => null,
            'firstName' => null,
            'lastName' => null,
            'stages' => [],
            'courses' => [],
        ];
        if ($infos['function'] === self::USER_TEACHER) {
            $infos['lastName'] = $document->getElementsByTagName(self::NODE_LASTNAME)->item(0)->nodeValue;
            $infos['firstName'] = $document->getElementsByTagName(self::NODE_FIRSTNAME)->item(0)->nodeValue;
            $infos['email'] = $document->getElementsByTagName(self::NODE_EMAIL)->item(0)->nodeValue;
        } else {
            $infos['firstName'] = $document->getElementsByTagName(self::NODE_STUDENT_USERNAME)->item(0)->nodeValue;
        }

        return $infos;
    }

    /**
     * @param array $infos
     *
     * @return array
     */
    private function getRoles(array $infos)
    {
        if ($infos['function'] == self::USER_TEACHER) {
            return [User::ROLE_TEACHER];
        } else {
            return [User::ROLE_STUDENT];
        }
    }
}