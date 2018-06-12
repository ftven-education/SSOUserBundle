<?php

namespace FTVEN\Education\SSOUserBundle\Test\Resources\Client;

use FTVEN\Education\SSOUserBundle\Client\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class TestClient
 *
 * @package FTVEN\Education\SSOUserBundle\Client
 */
class TestClient implements ClientInterface
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

    public function get($uri, array $params)
    {
        if (!isset($params['ticket']) || $params['ticket'] == 'bad') {
            throw new \Exception('Bad Token');
        }
        $return = '';
        switch ($params['ticket']) {
            case "badXml":
                $return = '</root>';
                break;
            case 'serviceNotFound':
                $return = '<root></root>';
                break;
            case 'badAuthentification':
                $return = '<cas:serviceResponse xmlns:cas=\'http://www.yale.edu/tp/cas\'><cas:badSuccess>Error</cas:badSuccess></cas:serviceResponse>';
                break;
            case "good-edutheque-teacher":
                $return = '<cas:serviceResponse xmlns:cas=\'http://www.yale.edu/tp/cas\'>
    <cas:authenticationSuccess>
	<cas:user>alexandre.lucas</cas:user>
	<cas:nom>Lucas</cas:nom>
	<cas:prenom>Alexandre</cas:prenom>
	<cas:email>alexandre.lucas@cndp.fr</cas:email>
	<cas:fonction>1</cas:fonction>
	<cas:idUser>11</cas:idUser>    
    </cas:authenticationSuccess>
</cas:serviceResponse>';
                break;
            case 'good-edutheque-student':
                $return = "<cas:serviceResponse xmlns:cas='http://www.yale.edu/tp/cas'>
<cas:authenticationSuccess>
<cas:user>elevesDeBruno</cas:user>
<cas:nom></cas:nom>
<cas:prenom></cas:prenom>
<cas:email></cas:email>
<cas:fonction>2</cas:fonction>
<cas:idUser>14</cas:idUser>
</cas:authenticationSuccess>
</cas:serviceResponse>";
                break;
        }
        return $return;
    }
}