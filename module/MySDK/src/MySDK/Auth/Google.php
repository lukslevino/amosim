<?php

namespace MySDK\Auth;

use Application\Service\Usuario as sUsuario;
use MySDK\Service\Google as sGoogle;
use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result;

class Google implements AdapterInterface {

    /**
     * 
     * @var $string
     */
    private $code;

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function authenticate() {
        try {
            $google = new sGoogle();
            $google->authenticate($this->code);

            $identity = (new sUsuario)->getPrepareIdentity($google->getInfo());
            if ($identity) {
                return new Result(Result::SUCCESS, $identity, array('OK'));
            }
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
