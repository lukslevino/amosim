<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbPessoa
 *
 * @ORM\Table(name="tb_pessoa")
 * @ORM\Entity
 */
class Pessoa
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_pessoa", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idPessoa;

    /**
     * @var string
     *
     * @ORM\Column(name="no_pessoa", type="string", length=255, nullable=false)
     */
    private $noPessoa;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_cpf", type="integer", nullable=false)
     */
    private $nuCpf;

    /**
     * @var integer
     *
     * @ORM\Column(name="tp_sexo", type="integer", nullable=false)
     */
    private $tpSexo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_nascimento", type="datetime", nullable=false)
     */
    private $dtNascimento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_telefone", type="integer", nullable=false)
     */
    private $nuTelefone;

    /**
     * @var integer
     *
     * @ORM\Column(name="nu_celular", type="integer", nullable=false)
     */
    private $nuCelular;

    /**
     * @return int
     */
    public function getIdPessoa()
    {
        return $this->idPessoa;
    }

    /**
     * @param int $idPessoa
     */
    public function setIdPessoa($idPessoa)
    {
        $this->idPessoa = $idPessoa;
        return $this;
    }

    /**
     * @return string
     */
    public function getNoPessoa()
    {
        return $this->noPessoa;
    }

    /**
     * @param string $noPessoa
     */
    public function setNoPessoa($noPessoa)
    {
        $this->noPessoa = $noPessoa;
        return $this;
    }

    /**
     * @return int
     */
    public function getNuCpf()
    {
        return $this->nuCpf;
    }

    /**
     * @param int $nuCpf
     */
    public function setNuCpf($nuCpf)
    {
        $this->nuCpf = $nuCpf;
        return $this;
    }

    /**
     * @return int
     */
    public function getTpSexo()
    {
        return $this->tpSexo;
    }

    /**
     * @param int $tpSexo
     */
    public function setTpSexo($tpSexo)
    {
        $this->tpSexo = $tpSexo;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDtNascimento()
    {
        return $this->dtNascimento;
    }

    /**
     * @param \DateTime $dtNascimento
     */
    public function setDtNascimento($dtNascimento)
    {
        $this->dtNascimento = $dtNascimento;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDtCadastro()
    {
        return $this->dtCadastro;
    }

    /**
     * @param \DateTime $dtCadastro
     */
    public function setDtCadastro($dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    /**
     * @return int
     */
    public function getNuTelefone()
    {
        return $this->nuTelefone;
    }

    /**
     * @param int $nuTelefone
     */
    public function setNuTelefone($nuTelefone)
    {
        $this->nuTelefone = $nuTelefone;
        return $this;
    }

    /**
     * @return int
     */
    public function getNuCelular()
    {
        return $this->nuCelular;
    }

    /**
     * @param int $nuCelular
     */
    public function setNuCelular($nuCelular)
    {
        $this->nuCelular = $nuCelular;
        return $this;
    }


}

