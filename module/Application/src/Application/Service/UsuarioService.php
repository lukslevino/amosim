<?php

namespace Application\Service;

use Application\Entity\Pessoa;
use MySDK\Service\AbstractService;
use MySDK\Form\Exception as fException;
use MySDK\Service\Mail;
use Application\Entity\Usuario as eUsuario;
use Application\Service\PerfilService as sPerfil;
use Zend\Json\Server\Smd\Service;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Zend\Math\Rand;
use MySDK\Auth\AdapterUpdate;
use Zend\Authentication\AuthenticationService;
use Application\Utils\Cpf as UtilCpf;
use Application\Service\Associado as sAssociado;

class UsuarioService extends AbstractService {

    const TP_AUTENTICACAO_FACEBOOK = 'auth_facebook';
    const TP_AUTENTICACAO_GOOGLE = 'auth_google';
    const TP_AUTENTICACAO_LOGIN = 'auth_login';

    protected $entity = "Application\Entity\Usuario";

    public static function deslogar() {
        $authService = self::getServiceManager()->get('AuthService');
        $authService->clearIdentity();

        $google = new \Zend\Session\Container('Google');
        $google->getManager()->getStorage()->clear();

        $facebook = new \Zend\Session\Container('Facebook');
        $facebook->getManager()->getStorage()->clear();
    }

    private function _prepareUsuarioRepository($info) {
        return $this->getRepository()->findOneByNuCpf($info->getNuCpf());
    }

    /**
     * 1º Faz o cadastro no caso da autenticacao por servido for com sucesso
     * 2º Atualiza os dados cadastrais no primeiro acesso
     * 3º Atualiza sempre a foto de perfil
     */
    private function _prepareUsuario($info) {
        return $this->_prepareUsuarioRepository($info);
    }

    private function _getPrepareIdentityUsuario($info) {
        $eUsuario = $this->_prepareUsuario($info);
        //Persistencia somente na autenticacao no google e facebook para poder atualizar a photo do profile
        if (!($info instanceof \Application\Entity\Usuario)) {
            $this->persist($eUsuario);
        }
        return $eUsuario;
    }

    public function getPrepareIdentityAcesso($eUsuario) {
        return $eUsuario->getAcesso()[0];
    }

    public function getPrepareIdentityPerfil($eUsuario) {
        $perfil = new \stdClass();

        $perfil->noRole = sPerfil::AUTENTICADO;

//        if (isset($eUsuario->getAcesso()[0])) {
//            $ePerfil = $eUsuario->getAcesso()[0]->getPerfil();
//
//            $perfil = new \stdClass();
//            $perfil->idPerfil = $ePerfil->getIdPerfil();
//            $perfil->noPerfil = $ePerfil->getNoPerfil();
//            $perfil->noRole = $ePerfil->getNoRole();
//        }
        return $perfil;
    }

    public function getPrepareIdentity($info) {
        $identity = new \stdClass();


        $identity->tp_autenticacao = self::TP_AUTENTICACAO_LOGIN;
        $eUsuario = $this->_getPrepareIdentityUsuario($info);
        $nome = explode(" ", $eUsuario->getNoUsuario());
        $nomeAbreviado = reset($nome).' '.end($nome);

        $user = new \stdClass();
        $user->idUsuario = $eUsuario->getIdUsuario();
        $user->idFacebook = '';//$eUsuario->getIdFacebook();
        $user->idGoogle = '';//$eUsuario->getIdGoogle();
        $user->noUsuarioAbreviado = $nomeAbreviado;
        $user->noUsuario = $eUsuario->getNoUsuario();
        $user->noEmail = $eUsuario->getNoEmail();
        $user->nuCpf = $eUsuario->getNuCpf();
        $user->urlImgPerfil = '';//$eUsuario->getUrlImgPerfil();
        $user->inActivationEmail = $eUsuario->getInActivationEmail();
        $user->idPerfil = $eUsuario->getPerfilEntity()->getIdPerfil();

        $sAssociado = new sAssociado();
        $user->dadosAssociados = $sAssociado->buscarDadosAssociado($eUsuario->getNuCpf());

        $identity->user = $user;

//        $identity->acesso = $this->getPrepareIdentityAcesso($identity->user);
       // $identity->perfil = $this->getPrepareIdentityPerfil($eUsuario);
        return $identity;
    }

    public function updateIdentity($eUsuario = null) {
        $user = $this->identity()->user;
        // instantiate the authentication service
        $auth = new AuthenticationService();
        // Set up the authentication adapter
        $authAdapter = new AdapterUpdate();
        $authAdapter->setUsername($user->noEmail);

        // Attempt authentication, saving the result
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            return $result;
        }

        return false;
    }

    public function gerarEnviarNovaSenha($email) {
        $this->beginTransaction();
        try {
            $eUsuario = $this->getRepository()->findOneByNoEmail($email);
            if ($eUsuario) {
                $dsSalt = base64_encode(Rand::getBytes(8, true));
                $senha = Rand::getString(8, '0123456789ABCDEFGHJKLMNPQRSTUVXZ');

                $eUsuario->setDsSalt($dsSalt);
                $eUsuario->setDsSenha($senha);
                $eUsuario->setDsActivationKey(md5($eUsuario->getNoEmail() . $eUsuario->getDsSalt()));

                $this->persist($eUsuario);

                $view = new ViewModel();
                $view->setTerminal(true);
                $view->setTemplate('mailer/enviar-nova-senha.phtml');
                $view->setVariable('eUsuario', $eUsuario);
                $view->setVariable('senha', $senha);

                $viewRender = self::getServiceManager()->get('ViewRenderer');
                $htmlEmail = $viewRender->render($view);

                $mail = new Mail();
                $mail->setFrom('eucondomino@gmail.com', 'Amocem');
                $mail->setSubject('Nova senha');
                $mail->setTo($eUsuario->getNoEmail());
                $mail->setMessage($htmlEmail);
                // Envia o e-mail.
                $mail->send();


                $this->commit();
                return $eUsuario;
            }
        } catch (Exception $e) {
            $this->close();
            $this->rollback();
            throw $e;
        }
    }

    public function enviarLinkAtivacao($eUsuario) {
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('mailer/inscreva-se-ja.phtml');
        $view->setVariable('eUsuario', $eUsuario);

        $viewRender = self::getServiceManager()->get('ViewRenderer');
        $htmlEmail = $viewRender->render($view);

        $mail = new Mail();
        $mail->setFrom('eucondomino@gmail.com', 'Amocem');
        $mail->setSubject('Link para ativação do cadastro');
        $mail->setTo($eUsuario->getNoEmail());
        $mail->setMessage($htmlEmail);
        // Envia o e-mail.
        $mail->send();
    }

    public function cadastrarUsuarioAdmin($data) {

        $this->beginTransaction();
        try {

            $uCpf = new UtilCpf();
            $nuCpf = $uCpf->retirarMascara($data['nu_cpf']);
            $eUsuario = $this->getRepository()->findOneByNuCpf($nuCpf);
            $sAssociado = new \Application\Service\Associado();

            if (null == $eUsuario) {
                $dtNascimentoFormat = new \DateTime($data['dt_nascimento']);
                $eUsuario = new eUsuario;
                $eUsuario->setNoUsuario($data['no_associado']);
                $eUsuario->setNoEmail($data['ds_email']);
                $eUsuario->setNuCpf($nuCpf);
                $eUsuario->setPerfilEntity($this->getEntityManager()->getReference('\Application\Entity\Perfil', Perfil::ASSOCIADO));
                $dsSenha = substr($nuCpf, 0, 6);
                $eUsuario->setDsSalt(base64_encode(Rand::getBytes(8, true)));
                $eUsuario->setDsSenha($dsSenha);
                $eUsuario->setInActivationEmail(0);
                $eUsuario->setDtNascimento($dtNascimentoFormat);
                $eUsuario->setDsActivationKey(md5($eUsuario->getNoEmail() . $eUsuario->getDsSalt()));
                $this->persist($eUsuario);
            }
//          $this->enviarLinkAtivacao($eUsuario);
            $sAssociado->update(['idUsuario' => $eUsuario->getIdUsuario()], $data['id_associado']);
            $this->commit();
            return $eUsuario->getIdUsuario();

        } catch (Exception $e) {
            $this->close();
            $this->rollback();
            throw $e;
        }
    }


    public function verificaCadastroExistente ($data) {
        $this->beginTransaction();
        try {

            $uCpf = new UtilCpf();
            $nuCpf = $uCpf->retirarMascara($data['cpf']);
            $sUsuario = new \Application\Service\UsuarioService;
            //$eUsuario = $this->getRepository()->findOneByNuCpf($nuCpf);

       /*     $podeCadastrar = false;
            if($sUsuario && null == $eUsuario ){
                $podeCadastrar = true;
            }

            if($eUsuario){
                throw new fException("O CPF informado já está cadastrado, entre em contato com suporte ou recupere sua senha de acesso.", 'nu_cpf');
            }*/

        /*    if($data['ds_senha'] != $data['ds_confirmar_senha'] ){
                throw new fException("A senha informada não confere", 'ds_senha');
            }

            if (null == $eUsuario) {*/


                /**
                 * @todo verificar a formatação
                 */
                $dataP = explode('/', $data["dtNascimento"]);
                $dataNoFormatoParaOBranco = $dataP[2].'-'.$dataP[1].'-'.$dataP[0];
                $date = strtotime($dataNoFormatoParaOBranco);
                $dtNascimento = date("Y-m-d", $date);
                $dtNascimentoFormat = new \DateTime($dtNascimento);


                $Pessoa = new \Application\Service\Pessoa();
                $ePessoa = new Pessoa();
                $ePessoa->setNoPessoa('Darlan Martins Dantas')
                    ->setNuCpf(1)
                    ->setDtNascimento($dtNascimentoFormat)
                    ->setTpSexo(1)
                    ->setNuCelular(9)
                    ->setNuTelefone(8)
                    ->setDtCadastro( new \DateTime());
                /** @var  Pessoa $idPessoa */
                $idPessoa = $Pessoa->saveProfile($ePessoa);
//                $this->persist($ePessoa);
                $this->commit();

//                $eUsuario = new eUsuario;
//                $eUsuario->setDsSenha($data['senha'])
//                    ->setIdPessoa($ePessoa->getIdPessoa())
//                    ->setIdPerfil($this->getEntityManager()->getReference('\Application\Entity\Perfil', PerfilService::USUARIO))
//                    ->setDtInclusao(new \DateTime());
//                $this->persist($eUsuario);


                var_dump($idPessoa->getIdPessoa());


             /*   if(is_null($rsAssociado)){
                    $sAssociado->salvarAssociadoUsuario($eUsuario);
                } else{
                    $sAssociado->update(['idUsuario' => $eUsuario->getIdUsuario()], $rsAssociado['idAssociado']);
                }*/
           // }
//            if (1 == $eUsuario->getInActivationEmail()) {
//                throw new fException("E-mail já se encontra ativado", 'email');
//            }

//            $eUsuario->setDsSalt(base64_encode(Rand::getBytes(8, true)));
//            $eUsuario->setDsSenha($data['password']);
//            $eUsuario->setInActivationEmail(0);
//            $eUsuario->setDsActivationKey(md5($eUsuario->getNuCpf() . $eUsuario->getDsSalt()));
//            $this->enviarLinkAtivacao($eUsuario);

            return true;
        } catch (\MySDK\Form\Exception $e) {
            var_dump($e);
            die;
            $this->close();
            $this->rollback();
            throw $e;
        } catch (Exception $e) {
            var_dump($e);
            die;
            $this->close();
            $this->rollback();
            throw $e;
        }
    }


    public function verificaRecuperarSenha($data)
    {
        $this->beginTransaction();
        try {

            $uCpf = new UtilCpf();
            $nuCpf = $uCpf->retirarMascara($data['nu_cpf']);
            $rsUsuario = $this->getRepository()->findOneByNuCpf($nuCpf);

            if (!$rsUsuario) {
                throw new fException("O CPF informado não está cadastrado em nosso sistema.", 'nu_cpf');
            }

            if ($data['ds_senha'] != $data['ds_confirmar_senha']) {
                throw new fException("A senha informada não confere", 'ds_senha');
            }

            if (null != $rsUsuario) {
                $dtNascimentoPost = date("Y-m-d", strtotime($data["dt_nascimento"]));
                if ($rsUsuario->getDtNascimento()->format('Y-m-d') != $dtNascimentoPost) {
                    throw new fException("A data de nascimento está diferente da informada no cadastro.", 'dt_nascimento');
                }
//                $eUsuario = new eUsuario;
//                $eUsuario->setDsSalt(base64_encode(Rand::getBytes(8, true)));
//                $eUsuario->setDsSenha($data['ds_senha']);
                $rsUsuario->setDsSalt(base64_encode(Rand::getBytes(8, true)));
                $rsUsuario->setDsSenha($data['ds_senha']);
                $this->persist($rsUsuario);
//                parent::update(['dsSenha' => $eUsuario->getDsSenha(),'dsSalt'=>$eUsuario->getDsSalt()], $rsUsuario->getIdUsuario());
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->close();
            $this->rollback();
            throw $e;
        }
    }


    public function activate($key) {
        $this->beginTransaction();
        try {
            $eUsuario = $this->getRepository()->findOneByDsActivationKey($key);
            if ($eUsuario && $eUsuario->getInActivationEmail() == 0) {
                $eUsuario->setInActivationEmail(1);
                $this->persist($eUsuario);
            }
            $this->commit();
            return $eUsuario;
        } catch (Exception $e) {
            $this->close();
            $this->rollback();
            throw $e;
        }
    }

}
