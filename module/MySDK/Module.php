<?php

namespace MySDK;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use MySDK\Service\AbstractService;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener ();
        $moduleRouteListener->attach($eventManager);

        /**
         * @todo Verificar a prioridade
         */
        // controle de erros
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array(
            $this,
            'handleError'
        ));
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array(
            $this,
            'handleError'
        ));

        // Verifica se o usuario tem autorizacao para utilizar o eutenista
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array(
            $this,
            'doAuthorization'
        ));

        // carrega o ServiceManager para o service
        AbstractService::setServiceManager($e->getTarget()->getServiceManager());

        // translator
        $this->configTranslator();
    }

    public function doAuthorization(MvcEvent $e) {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();

        $router = $sm->get('router');
        $request = $sm->get('request');

        $matchedRoute = $router->match($request);
        if (null !== $matchedRoute) {
            $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function ($e) use($sm) {
                return $sm->get('ControllerPluginManager')
                                ->get('AuthorizationPlugin')
                                ->setAclService($sm->get('AclService'))
                                ->setAuthService($sm->get('AuthService'))
                                ->doAuthorization($e);
            }, 2);
        }
    }

    public function configTranslator() {
        // Cria o translator
        $translator = new \Zend\Mvc\I18n\Translator(new \Zend\I18n\Translator\Translator());
        // Adiciona o arquivo de tradução
        $translator->addTranslationFile('phpArray', __DIR__ . '\language/zend-i18n-resources/languages/pt_BR/Zend_Validate.php', 'default', 'pt_BR');
        // Define o tradutor padrão dos validadores
        return \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'AclService' => function ($sm) {
                    $config = $sm->get('Config');
                    return new \MySDK\Service\Acl($config ['acl']);
                },
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config ['session'])) {
                        $session = $config ['session'];

                        $sessionConfig = null;
                        if (isset($session ['config'])) {
                            $class = isset($session ['config'] ['class']) ? $session ['config'] ['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session ['config'] ['options']) ? $session ['config'] ['options'] : array();
                            $sessionConfig = new $class ();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session ['storage'])) {
                            $class = $session ['storage'];
                            $sessionStorage = new $class ();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session ['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session ['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                    } else {
                        $sessionManager = new SessionManager ();
                    }
                    return $sessionManager;
                }
                    )
                );
            }

            public function handleError(MvcEvent $e) {
             /*   $exception = $e->getParam('exception');
                $locator = $e->getApplication()->getServiceManager();
                $authService = $locator->get('AuthService');
                $config = $locator->get('Config');
                // @todo: tratar exception para produção
                // $acl = new AclService($config);

                $viewModel = $e->getViewModel();
                $viewModel->setTerminal(true);
                // $viewModel->setVariables(array('acl' => $acl));
                //dumpd($locator);
                $exceptionstrategy = $locator->get('ViewManager')->getExceptionStrategy();*/
                $exception = $e->getParam('exception');
            }

        }
        