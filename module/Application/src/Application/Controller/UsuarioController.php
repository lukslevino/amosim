<?php
/**
 * Created by PhpStorm.
 * User: lazevedol
 * Date: 20/11/2017
 * Time: 23:42
 */

namespace Application\Controller;


use Application\Service\UsuarioService;
use Application\Utils\Cpf as UtilCpf;
use Zend\Authentication\AuthenticationService;
use Zend\Captcha\Dumb;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MySDK\Auth\Adapter as aAdapter;

class UsuarioController extends AbstractActionController
{
    public function solicitarAcessoAction()
    {
        /** @var UsuarioService $usuarioService */
        //$usuarioService = $this->getServiceLocator()->get('usuarioService');

        $view = new ViewModel();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            $auth = new AuthenticationService();

            $authAdapter = new aAdapter();

            $uCpf = new UtilCpf();
            $nuCpf = $uCpf->retirarMascara($data['nu_cpf']);
            $authAdapter->setUsername($nuCpf);
            $authAdapter->setPassword($data['ds_senha']);

            $result = $auth->authenticate($authAdapter);

            if ($data['cpf']) {

                $entUsuario = $usuarioService->findOneBy(['nu_cpf' => $post['cpf']]);

                if (!empty($entUsuario) && $entUsuario->getCoUsuario()) {
                    //msg de erro
                    $view->setTerminal(true);
                    $view->setTemplate('application/ajax/vazio.phtml');
                    return $view;
                }
            }
        }

        return $view;

    }

    public function acessoAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('/application/usuario/acesso.phtml');
        return $viewModel;
    }

    public function cadastrarAction()
    {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $srvUsuario = new \Application\Service\UsuarioService;
                if ($srvUsuario->verificaCadastroExistente($data)) {
                    $this->flashMessenger()->addMessage('Cadastro realizado com sucesso', 'sucesso');
                   // return $this->redirect()->toRoute('home');
                }
            } else {
                $this->flashMessenger()->addMessage('Foram encontrados um ou mais erros. Corrija os campos abaixo', 'error');
            }
        } catch (\MySDK\Form\Exception $ex) {
            die('error');

        }
        $this->layout('/usuario/acesso.phtml');

        $view = new ViewModel();
        return $view;
    }
}