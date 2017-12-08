<?php

namespace MySDK\Auth;

use Application\Entity\Usuario as eUsuario;
use MySDK\Service\Facebook as sFacebook;
use Application\Service\Usuario as sUsuario;
use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result;

class Facebook implements AdapterInterface {

    public function authenticate() {
        try {
            $identity = (new sUsuario)->getPrepareIdentity((new sFacebook())->getInfo());
            if ($identity) {
                return new Result(Result::SUCCESS, $identity, array('OK'));
            }
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());
        } catch (\FacebookSDKException $ex) {
            throw $ex;
        }
    }

}
