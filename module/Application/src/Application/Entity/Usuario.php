<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbUsuario
 *
 * @ORM\Table(name="tb_usuario", indexes={@ORM\Index(name="fk_tb_usuario_tb_pessoa_idx", columns={"id_pessoa"}), @ORM\Index(name="fk_tb_usuario_tb_perfil1_idx", columns={"id_perfil"})})
 * @ORM\Entity
 */
class Usuario
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_senha", type="string", length=45, nullable=false)
     */
    private $dsSenha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_inclusao", type="datetime", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_autenticacao", type="datetime", nullable=true)
     */
    private $dtAutenticacao;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_salt", type="string", length=255, nullable=true)
     */
    private $dsSalt;

    /**
     * @var integer
     *
     * @ORM\Column(name="in_activation_email", type="integer", nullable=true)
     */
    private $inActivationEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="ds_activation_key", type="string", length=255, nullable=true)
     */
    private $dsActivationKey;

    /**
     * @var \Perfil
     *
     * @ORM\ManyToOne(targetEntity="Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_perfil", referencedColumnName="id_perfil")
     * })
     */
    private $idPerfil;

    /**
     * @var \Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pessoa", referencedColumnName="id_pessoa")
     * })
     */
    private $idPessoa;

    /**
     * @return int
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param int $idUsuario
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    /**
     * @return string
     */
    public function getDsSenha()
    {
        return $this->dsSenha;
    }

    /**
     * @param string $dsSenha
     */
    public function setDsSenha($dsSenha)
    {
        $this->dsSenha = $dsSenha;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDtInclusao()
    {
        return $this->dtInclusao;
    }

    /**
     * @param \DateTime $dtInclusao
     */
    public function setDtInclusao($dtInclusao)
    {
        $this->dtInclusao = $dtInclusao;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDtAutenticacao()
    {
        return $this->dtAutenticacao;
    }

    /**
     * @param \DateTime $dtAutenticacao
     */
    public function setDtAutenticacao($dtAutenticacao)
    {
        $this->dtAutenticacao = $dtAutenticacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getDsSalt()
    {
        return $this->dsSalt;
    }

    /**
     * @param string $dsSalt
     */
    public function setDsSalt($dsSalt)
    {
        $this->dsSalt = $dsSalt;
        return $this;
    }

    /**
     * @return int
     */
    public function getInActivationEmail()
    {
        return $this->inActivationEmail;
    }

    /**
     * @param int $inActivationEmail
     */
    public function setInActivationEmail($inActivationEmail)
    {
        $this->inActivationEmail = $inActivationEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getDsActivationKey()
    {
        return $this->dsActivationKey;
    }

    /**
     * @param string $dsActivationKey
     */
    public function setDsActivationKey($dsActivationKey)
    {
        $this->dsActivationKey = $dsActivationKey;
        return $this;
    }

    /**
     * @return \Perfil
     */
    public function getIdPerfil()
    {
        return $this->idPerfil;
    }

    /**
     * @param \Perfil $idPerfil
     */
    public function setIdPerfil($idPerfil)
    {
        $this->idPerfil = $idPerfil;
        return $this;
    }

    /**
     * @return \Pessoa
     */
    public function getIdPessoa()
    {
        return $this->idPessoa;
    }

    /**
     * @param \Pessoa $idPessoa
     */
    public function setIdPessoa($idPessoa)
    {
        $this->idPessoa = $idPessoa;
        return $this;
    }


}

