<?php

namespace Application\Service;

use MySDK\Service\AbstractService;

class PerfilService extends AbstractService {

    /**
     * Role Perfis
     */
    const PUBLICO = 'publico';
    const AUTENTICADO = 'autenticado';
    const ADMINISTRADOR = 1;
    const USUARIO = 2;
    const SUPER = 3;

    protected $entity = "Application\Entity\Perfil";

}
