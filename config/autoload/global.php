<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    'mail' => array(
        'debug' => false,
        'file_options' => array(
            'path' => APPLICATION_PATH . '/data/mail',
            'callback' => function () {
                return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
            },
        ),
        'smtp_options' => array(
            'host' => 'smtp.administracao.servicos.ws',
            'connection_class' => 'login',
            'connection_config' => array(
                //'ssl' => 'tls',
                'username' => 'amocem@administracao.servicos.ws',
                'password' => 'forever354256'
            ),
            'port' => 587
        )
    ),
    'dir' => array(
        'associados' => array(
            'cadastro' => 'C:\Zend\Apache2\htdocs\amocem.local\data\upload',
        ),
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'eutenista'
            )
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent'
        )
    )
);
