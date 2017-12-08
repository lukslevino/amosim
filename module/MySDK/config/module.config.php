<?php
namespace MySDK;

return array(
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'AuthService'
        ),
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory'
        ),
        'invokables' => array(
            'AuthService' => 'Zend\Authentication\AuthenticationService'
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'AuthorizationPlugin' => 'MySDK\Controller\Plugin\AuthorizationPlugin'
        )
    ),
		//Application/config/module.config.php
		'translator' => array(
				'locale' => 'nl_BE',
				'translation_file_patterns' => array(
						array(
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo'
						),
				),
		),
		
		
    'translator' => array (
        'locale' => 'pt_BR',

        'translation_file_patterns' => array(
            array(
                'type'       => 'phpArray',
                'base_dir'   => __DIR__ . '/../../../vendor/zendframework/zendframework/resources/languages',
                'pattern'    => '%s/Zend_Validate.php',
                'text_domain' => __NAMESPACE__, // Sem isso, o textDomain, usado pelo Zend\I18n\Translator\Translator fica 'default' e como o 'default' já foi definido quando foi adicionado no Application/config/module.config.php há um conflito e fica prevalecendo o do modulo Application
            ),
        ),
    ),
		
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'form' => 'MySDK\Form\View\Helper\Form',
            'formBasic' => 'MySDK\Form\View\Helper\FormBasic',
            'formfield' => 'MySDK\Form\View\Helper\FormField',
            'field'=>'MySDK\Form\View\Helper\Field'
        )
    )
);
