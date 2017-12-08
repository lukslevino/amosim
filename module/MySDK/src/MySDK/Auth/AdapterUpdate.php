<?php

namespace MySDK\Auth;

use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result;
use Application\Service\Usuario as sUsuario;

class AdapterUpdate implements AdapterInterface {

    protected $username;

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function authenticate() {
        $sUsuario = new sUsuario();
        $eUsuario = $sUsuario->getRepository()->findOneByNoEmail($this->getUsername());

        if (null == $eUsuario)
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());

        return new Result(Result::SUCCESS, $sUsuario->getPrepareIdentity($eUsuario), array('OK'));
    }

}
