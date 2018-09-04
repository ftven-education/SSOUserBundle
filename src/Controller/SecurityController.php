<?php

namespace FTVEN\Education\SSOUserBundle\Controller;

use FTVEN\Education\SSOUserBundle\Service\Connector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class SecurityController
 *
 * @package FTVEN\Education\SSOUserBundle\Controller
 */
class SecurityController extends Controller
{
    /**
     * @param Request $request
     * @param string  $service
     *
     * @return RedirectResponse
     */
    public function loginAction(Request $request, $service)
    {
        $callback = $request->get('callback');
        $params = ['service' => $service];
        if ($callback !== null) {
            $params["callback"] = $callback;
        }
        $logger = $this->get('monolog.logger.security');
        /** @var Connector $connector */
        $connector = $this->get('sso_user.connector.pool')->getConnector($service);
        $url = urlencode($this->generateUrl('login', $params, UrlGeneratorInterface::NETWORK_PATH));
	      $url = sprintf('%s?renew=true&service=%s%s', $connector->getLoginUrl(), 'https://', $url);
        $logger->debug("Redirection to CAS's server", ['url' => $url, 'connector' => $service]);

        return $this->redirect($url);
    }

    /**
     * @codeCoverageIgnore
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
