<?php

namespace MySDK\Controller\Plugin;

use Application\Service\PerfilService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use MySDK\Service\Acl;
use Application\Service\Pessoa as sPessoa;
use Application\Service\Perfil;

class AuthorizationPlugin extends AbstractPlugin {

    /**
     *
     * @var \Base\Service\Acl
     */
    private $sAcl;

    /**
     *
     * @var \Zend\Authentication\AuthenticationService
     */
    private $sAuth;
    private $isFlashmessage = false;

    public function setAclService(Acl $acl) {
        $this->sAcl = $acl;
        return $this;
    }

    public function setAuthService(AuthenticationService $auth) {
        $this->sAuth = $auth;
        return $this;
    }

    public function getResoureAtual(MvcEvent $e) {
        $controller = $e->getTarget();
        $route = $controller->getEvent()
                ->getRouteMatch()
                ->getParams();
        // Colocando tudo minusculo
        $controller = strtolower($route['controller']);
        // Retirando a palavra \controller o resultado do para nao ter que mapear isso no acl.global.php
        $controller = str_replace("\controller", "", $controller);
        // concatenando com a action
        $resource = $controller . '\\' . strtolower($route['action']);
        return $resource;
    }

    public function doAuthorization(MvcEvent $e) {
        $controller = $e->getTarget();
        $resource = $this->getResoureAtual($e);

        $permissions = PerfilService::PUBLICO;
      //  $permissions = $this->sAuth->getIdentity()->user->idPerfil;
        $identity = $this->sAuth->getIdentity();

        if (false != $this->sAuth->hasIdentity() &&
                isset($identity->user->idPerfil)) {

            /**
             * *
             * Houve a necessidade de adicionar o role publico e autenticado nos roles do identity
             * devido a herenca dos roles do acl da zend nao esta funcionando.
             *
             * Caso funcione um dia trocar por esse codigo
             * $permissions = $this->sAuth->getIdentity()->permissions;
             * $permissions = array_merge($permissions, $identity->permissions);
             */
//            $permissions = $this->sAuth->getIdentity()->permissions;
//            $permissions = array_merge($permissions, $identity->permissions);
            $permissions = $identity->user->idPerfil;
        }

        $this->sAcl->loadConfigurations($permissions);
        /**
         * Verifica se o recurso é acessível se não houver entidade pessoa
         * Se não existir a entidade pessoa e existiver acessando uma funcionalidade que diferente de publico e autenticado
         * redireciona apra o profile.
         */
//        if ($this->sAuth->hasIdentity()) {
//            $sPessoa = new sPessoa();
//            $ePessoa = $sPessoa->getRepository()->findBy(['usuario' => $identity->user->getIdUsuario()]);
//            if (null == $ePessoa &&
//                    false == $this->sAcl->isFeaturesAccessibleIfThereIsNoPerson($resource)) {
//                $controller->flashMessenger()->addErrorMessage('Para continuar o acesso no Eu condomino existe a necessidade de cadastrar os dados pessoais.');
//                return $controller->redirect()->toRoute('profile');
//            }
//        }



//        if ($this->sAuth->hasIdentity()) {
//            // Se o usuario nao estiver com a Meu perfil completo(Telefone e Whatsapp) redireciona para meu profile
//            if ((null == $identity->user->getNuCelular() || null == $identity->user->getNuWhatsapp()) && ($resource != 'application\usuario\profile' && $resource != 'application\usuario\logoff' && $resource != 'application\usuario\resolve')) {
//                $controller->flashMessenger()->addErrorMessage('Para continuar o acesso no Eu tenista, existe a necessidade de cadastrar o número de telefone e whatsapp.');
//                return $controller->redirect()->toRoute('profile');
//            }
//        }
        // Se tiver acessando um recurso que nao exsite
        if (false == $this->sAcl->hasResource($resource)) {
            debug($resource);
            debug('resorce nao existe', 1);
            $this->sAuth->clearIdentity();
            $controller->flashMessenger()->addErrorMessage('Acesso negado');
            return $controller->redirect()->toRoute('home');
        }

//         $perfilAtenticado = 'incompleto';
        // Caso a autorizacao do Acl seja falso, redireciona para home apagando o identity.
        if (false == $this->sAcl->isAllowed($permissions, $resource)) {
            if (isset($identity->ligaSelecionada)) {
                $controller->flashMessenger()->addErrorMessage('Acesso negado, por favor nao faça mais isso!!!');
                return $controller->redirect()->toRoute('condominio/visualizar', array('idLiga' => $identity->ligaSelecionada));
            }
            debug('Voce nao tem acesso');
            debug($permissions);
            debug($resource);
            debug($identity, 1);
            if ($this->sAuth->hasIdentity()) {
                return $controller->redirect()->toRoute('access-denied');
            } else {
                $this->sAuth->clearIdentity();
                $controller->flashMessenger()->addErrorMessage('Acesso negado');
                return $controller->redirect()->toRoute('home');
            }
        }
    }

}
