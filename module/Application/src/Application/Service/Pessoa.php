<?php

namespace Application\Service;

use MySDK\Service\AbstractService;
use MySDK\Form\Exception as fException;
use MySDK\Form\Form;
use Zend\Stdlib\Hydrator;
use Application\Service\Usuario as sUsuario;

class Pessoa extends AbstractService {

    const TP_SEXO_FEMI = 1;
    const TP_SEXO_MASC = 2;
    const TP_SEXO_OUTR = 3;

    protected $entity = "Application\Entity\Pessoa";

    public static function getListTpSexo() {
        return [
            self::TP_SEXO_FEMI => 'Feminino',
            self::TP_SEXO_MASC => 'Masculino',
        ];
    }

    public static function getArrTpSexo() {
        return [
            self::TP_SEXO_FEMI,
            self::TP_SEXO_MASC,
        ];
    }

    public function saveProfile($data) {
        $this->beginTransaction();
        try {
//            $data["dt_nascimento"] = new \DateTime($data["dt_nascimento"]);
//
//            $idUsuario = $this->identity()->user->idUsuario;
//            $data['usuario'] = $this->getEntityManager()->getReference('\Application\Entity\Usuario', $idUsuario);
//
//            $sUsuario = new sUsuario();
//
//            $eUsuario = $sUsuario->find($idUsuario);
//            $eUsuario->setNoUsuario($data['no_usuario']);
//
//
//            if (null == $eUsuario->getPessoa()) {
//                $ePessoa = new $this->entity($data);
//            } else {
//                $ePessoa = $eUsuario->getPessoa();
//            }
//
//            $ePessoaDb = $this->getRepository()->findOneByNuCpf($data['nu_cpf']);
//            if (null != $ePessoaDb && ($ePessoaDb->getIdPessoa() != $ePessoa->getIdPessoa() || null == $ePessoa->getIdPessoa())) {
//                throw new fException("CPF já cadastrado", 'nu_cpf');
//            }


//            $ePessoa->setNoPessoa($data['nome'])
//                ->setNuCpf(1)
//                ->setDtNascimento($data['dataNascumento'])
//                ->setTpSexo(1)
//                ->setNuCelular(9)
//                ->setNuTelefone(8)
//                ->setDtCadastro( new \DateTime());
//            $ePessoa = new $this->entity($data);
//            $this->persist($ePessoa);
            $this->persist($data);
            //$ePessoa = new $this->entity($data);
            $this->commit();

            return $data;
        } catch (\MySDK\Form\Exception $e) {
            $this->close();
            $this->rollback();
            throw $e;
        } catch (\Exception $e) {
            $this->close();
            $this->rollback();
            throw $e;
        }
    }

}
