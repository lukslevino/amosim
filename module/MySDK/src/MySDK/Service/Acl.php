<?php

namespace MySDK\Service;

use Application\Service\Perfil as sPerfil;
use Zend\Permissions\Acl\Acl as ZAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Acl extends ZAcl {

    protected $config;

    /**
     * Carrega toda a configuração do ACL
     *
     * @param array $config
     * @param array $permissions
     * @return \Base\Controller\Plugin\AclPlugin
     */
    public function __construct($config) {
        $this->config = $config;
        return $this;
    }

    /**
     * Carrega as configuracoes do ACL
     *
     * @param array $permissions
     */
    public function loadConfigurations($permissions) {
        $this->loadRoles();
        $this->loadPermissions($permissions);
    }

    /**
     * Carrega Roles
     */
    public function loadRoles() {
        $perfils = $this->config['perfis'];
        foreach ($perfils as $name => $parents) {
            if (!$this->hasRole($name)) {
                $this->addRole(new Role($name), $parents);
            }
        }
    }

    /**
     * Carrega a coleção de permissoes do usuário
     *
     * @param array $permissions
     */
    public function loadPermissions($permissions) {
        $resources = $this->config['resources'];
        foreach ($resources as $resource => $role) {
            //Add o resource
            if (!$this->hasResource($resource)) {
                $this->addResource(new Resource($resource));
            }
//             //Add o role
//             if (!$this->hasRole($role)) {
//                 $this->addRole(new Role($role));
//             }
            //Add a permissao no resouce
            $this->allow($role, $resource);
        }
    }

    /**
     * Carrega a coleção de permissoes do usuário
     *
     * @param array $permissions
     */
    public function isFeaturesAccessibleIfThereIsNoPerson($feature) {
        $_resources = [];
        $resources = $this->config['resources'];
        foreach ($resources as $resource => $perfis) {
            if (in_array(sPerfil::PUBLICO, $perfis))
                $_resources[] = $resource;

            if (in_array(sPerfil::AUTENTICADO, $perfis))
                $_resources[] = $resource;
        }

        return in_array($feature, $_resources) ? true : false;
    }

    /**
     * Recuperar os eventos do resource
     *
     * @param string $resource
     * @return void|multitype:Ambigous <array>
     */
    public function getEventosFuncionalidade($resource) {
        if (array_key_exists($resource, $this->config['permissoes'])) {
            $funcionalidades = $this->config['permissoes'][$resource];
            $arrEvento = array();
            foreach ($this->config['eventos'] as $coEvento => $arrFuncionalidadeDoEvento) {
                $coEventoAtivo = null;
                foreach ($funcionalidades as $funcionalidade) {
                    if (in_array($funcionalidade, $arrFuncionalidadeDoEvento)) {
                        $coEventoAtivo = $coEvento;
                    }
                }
                if ($coEventoAtivo) {
                    $arrEvento[] = $coEventoAtivo;
                }
            }
            return $arrEvento;
        }
        return;
    }

    /**
     * Verifica se o evento da funcionalidade esta disponivel
     * caso a funcionalidade nao tenha evento, ele e disponivel por default
     *
     * @param string $resource
     * @return boolean
     */
    public function isEventoDisponivel($resource) {
        if (false == $this->hasResource($resource)) {
            return false;
        }

        $eventos = $this->getEventosFuncionalidade($resource);
        // Caso a funcionalidade nao tenha evento, ele estara disponivel
        if (0 == count($eventos)) {
            return true;
        }
        foreach ($eventos as $evento) {
            if (\Application\Service\Cronograma::getInstance()->isEventoDisponivel($evento)) {
                return true;
            }
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Permissions\Acl\Acl::isAllowed()
     */
//     public function isAllowed($permissions = null, $resource = null, $privilege = null)
//     {
//         if (false == $this->hasResource($resource)) {
//             return false;
//         }
//         foreach ($permissions as $permission) {
//             if (parent::isAllowed($permission, $resource)) {
//                 return true;
//             }
//         }
//         return false;
//     }

    public function isResourceForAutenticado($resource) {
        if (array_key_exists($resource, $this->config['permissoes'])) {
            $funcionalidades = $this->config['permissoes'][$resource];
            return in_array(\Application\Service\Funcionalidade::K_AUTENTICADO, $funcionalidades) || in_array(\Application\Service\Funcionalidade::K_PUBLICO, $funcionalidades) ? true : false;
        }
        return;
    }

}
