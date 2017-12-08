<?php

use Application\Service\PerfilService;

return [
    'acl' => [
        /**
         * Aqui estara o mapeamento o perfil que o o usuario ira receber
         */
        'perfis' => [
            PerfilService::PUBLICO => [],
            PerfilService::AUTENTICADO => [
                PerfilService::PUBLICO
            ],
            PerfilService::USUARIO => [
                PerfilService::PUBLICO,
                PerfilService::AUTENTICADO
            ],

            PerfilService::ADMINISTRADOR => [
                PerfilService::PUBLICO,
                PerfilService::AUTENTICADO,
                PerfilService::USUARIO
            ],
            PerfilService::SUPER => [
                PerfilService::PUBLICO,
                PerfilService::AUTENTICADO,
                PerfilService::ADMINISTRADOR,
            ],
        ],
        'resources' => [
            //publico
            'application\index\index' => [PerfilService::PUBLICO],
            'application\index\logoff' => [PerfilService::PUBLICO],
            'application\index\googlecallback' => [PerfilService::PUBLICO],
            'application\index\fbcallback' => [PerfilService::PUBLICO],
            'application\index\naoconsegueacessar' => [PerfilService::PUBLICO],
            'application\index\enviarlinkativacao' => [PerfilService::PUBLICO],
            'application\index\inscrevaseja' => [PerfilService::PUBLICO],
            'application\index\activate' => [PerfilService::PUBLICO],
            'application\index\enviarsenha' => [PerfilService::PUBLICO],
            'application\index\recuperarsenha' => [PerfilService::PUBLICO],
            'application\usuario\cadastrar' => [PerfilService::PUBLICO],
            'application\usuario\solicitar-acesso' => [PerfilService::PUBLICO],


            //Autenticado
            'application\index\resolve-usuario' => [PerfilService::AUTENTICADO],
            'application\pessoa\profile' => [PerfilService::AUTENTICADO],
            'application\index\inicio' => [PerfilService::AUTENTICADO],
            'application\index\dados-associados' => [PerfilService::USUARIO],
            'application\index\documentacao' => [PerfilService::USUARIO],
            'application\index\comprovante-pagamento' => [PerfilService::USUARIO],
            'application\index\teste-abas' => [PerfilService::USUARIO],
            'application\index\comprovante-pagamento' => [PerfilService::USUARIO],
            'application\index\download-documentacao' => [PerfilService::USUARIO],


            'application\index\cadastrar-associado' => [PerfilService::ADMINISTRADOR],
            'application\index\consultar-cooperado' => [PerfilService::ADMINISTRADOR],
            'condominio\index\criar-liga-estatuto' => [PerfilService::ADMINISTRADOR],


            //Associado
            /*apos definir perfil usuario*/

        ]
    ]
];
