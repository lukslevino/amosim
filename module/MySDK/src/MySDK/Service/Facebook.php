<?php

namespace MySDK\Service;

use Zend\Session\Container;

class Facebook extends AbstractService {

    /**
     *
     * @var String
     */
    private $appId;

    /**
     *
     * @var String
     */
    private $appSecret;

    /**
     *
     * @var String
     */
    private $defaultGraphVersion;

    /**
     *
     * @var \Facebook\Facebook
     */
    private $fb;

    /**
     *
     * @var array
     */
    private $permissions;

    /**
     *
     * @var String
     */
    private $callback;

    public function __construct() {
        $config = $this->getServiceManager()->get('Config')['facebook'];
        $arrayConfigConection['app_id'] = $config['app_id'];
        $arrayConfigConection['app_secret'] = $config['app_secret'];
        $arrayConfigConection['default_graph_version'] = $config['default_graph_version'];

        $this->callback = $config['callback'];
        $this->permissions = $config['permissions'];
        $this->fb = new \Facebook\Facebook($arrayConfigConection);
    }

    public function getLoginUrl() {
        $helper = $this->fb->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($this->callback, $this->permissions);
        return htmlspecialchars($loginUrl);
    }

    public function getLogoutUrl() {
        $helper = $this->fb->getRedirectLoginHelper();
        $next_uri = 'http://' . $_SERVER['SERVER_NAME'];
        $logoutUrl = $helper->getLogoutUrl($this->getAccessToken(), $next_uri);
        return htmlspecialchars($logoutUrl);
    }

    public function getAccessToken() {
        $sessionFacebook = new Container('Facebook');
        if (null == $sessionFacebook->accessToken) {
            try {
                $helper = $this->fb->getRedirectLoginHelper();
                if (isset($_GET['state'])) {
//                    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
                }
                $accessToken = $helper->getAccessToken();
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                throw new \Facebook\Exceptions\FacebookSDKException('Graph returned an error: ' . $e->getMessage());
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                throw new \Facebook\Exceptions\FacebookSDKException('Facebook SDK returned an error: ' . $e->getMessage());
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            if (!isset($accessToken)) {
                if ($helper->getError()) {
                    throw new \Facebook\Exceptions\FacebookSDKException('HTTP/1.0 401 Unauthorized');
                    header('HTTP/1.0 401 Unauthorized');
                    echo "Error: " . $helper->getError() . "\n";
                    echo "Error Code: " . $helper->getErrorCode() . "\n";
                    echo "Error Reason: " . $helper->getErrorReason() . "\n";
                    echo "Error Description: " . $helper->getErrorDescription() . "\n";
                } else {
                    throw new \Facebook\Exceptions\FacebookSDKException('HTTP/1.0 400 Bad Request');
                    header('HTTP/1.0 400 Bad Request');
                    echo 'Bad request';
                }
                exit;
            }

            $sessionFacebook->FBRLH_state = $_SESSION['FBRLH_state'];
            $sessionFacebook->accessToken = $accessToken;
        }
        return $sessionFacebook->accessToken;
    }

    public function getInfo() {
        $accessToken = $this->getaccessToken();
        try {
            $this->fb->setDefaultAccessToken($accessToken->getValue());
            // Returns a `Facebook\FacebookResponse` object
            //picture types small, normal, album, large, square
            $response = $this->fb->get('/me?fields=id,gender,name,email,picture.type(normal),birthday');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            throw new \Facebook\Exceptions\FacebookSDKException('Graph returned an error: ' . $e->getMessage());
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            throw new \Facebook\Exceptions\FacebookSDKException('Facebook SDK returned an error: ' . $e->getMessage());
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if ($response->isError())
            return false;

        return $response->getGraphUser();
    }

}
