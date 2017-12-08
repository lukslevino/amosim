<?php

namespace MySDK\Auth;

use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result;
use Application\Service\Usuario as sUsuario;
use Application\Entity\Usuario as eUsuario;

class Adapter implements AdapterInterface {

    protected $username;
    protected $password;

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function authenticate() {
        $sUsuario = new sUsuario();
        $eUsuario = $sUsuario->getRepository()->findOneByNuCpf($this->getUsername());

        if (null == $eUsuario)
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());

        $eLogin = new eUsuario;
        $eLogin->setDsSalt($eUsuario->getDsSalt());
        $eLogin->setDsSenha($this->getPassword());

        if ($eUsuario->getDsSenha() == $eLogin->getDsSenha()) {
            return new Result(Result::SUCCESS, $sUsuario->getPrepareIdentity($eUsuario), array('OK'));
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, array());
        }
    }

}
