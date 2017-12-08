<?php

namespace MySDK\Service;

use Zend\Session\Container;

class Google extends AbstractService {

    /**
     *
     * @var String
     */
    private $clientId;

    /**
     *
     * @var String
     */
    private $clientSecret;

    /**
     * 
     * @var String
     */
    private $applicationId;

    /**
     * 
     * @var String
     */
    private $applicationName;

    /**
     *
     * @var \Google_Client
     */
    private $client;

    /**
     *
     * @var array
     */
    private $scope;

    /**
     *
     * @var String
     */
    private $callback;

    public function __construct() {
        $config = $this->getServiceManager()->get('Config')['google'];
        $this->client = new \Google_Client();
        $this->client->setApplicationName($config['applicationName']);
        $this->client->setClientId($config['app_id']);
        $this->client->setClientSecret($config['app_secret']);
        $this->client->setRedirectUri($config['callback']);
        /**
         * Utilizado so na versao 2 da api do google, mas esta dando erro no retorno do usuario logado.
         * $this->client->setHttpClient(new \GuzzleHttp\Client(array('verify' => false, 'base_uri'=>\Google_Client::API_BASE_PATH)));
         */
        $this->client->setScopes($config['scope']);
    }

    public function getLoginUrl() {
        $loginUrl = $this->client->createAuthUrl();
        return htmlspecialchars($loginUrl);
    }

    public function authenticate($code) {
        try {
            $this->client->authenticate($code);
            $this->setAccessToken($this->client->getAccessToken());
        } catch (\Google_Auth_Exception $e) {
            throw new \Google_Auth_Exception($e->getMessage());
        }
    }

    public function setAccessToken($accessToken) {
        $sessionGoogle = new Container('Google');
        $sessionGoogle->accessToken = $accessToken;
    }

    public function getAccessToken() {
        $sessionGoogle = new Container('Google');
        return $sessionGoogle->accessToken;
    }

    public function getInfo() {
        try {
            $this->client->setAccessToken($this->getAccessToken());
            $oauth = new \Google_Service_Oauth2($this->client);
            return $oauth->userinfo->get();
        } catch (\Google_Auth_Exception $e) {
            throw new \Google_Auth_Exception('Goole SDK returned an error: ' . $e->getMessage());
            echo 'Goole SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }

}
