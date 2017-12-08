<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbIndicacao
 *
 * @ORM\Table(name="tb_indicacao", indexes={@ORM\Index(name="fk_tb_indicacao_tb_usuario1_idx", columns={"id_usuario"}), @ORM\Index(name="fk_tb_indicacao_tb_pessoa1_idx", columns={"id_pessoa"})})
 * @ORM\Entity
 */
class Indicacao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_indicacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idIndicacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_indicacao", type="datetime", nullable=false)
     */
    private $dtIndicacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="clinica_filial", type="integer", nullable=false)
     */
    private $clinicaFilial;

    /**
     * @var \Pessoa
     *
     * @ORM\ManyToOne(targetEntity="TbPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_pessoa", referencedColumnName="id_pessoa")
     * })
     */
    private $idPessoa;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="TbUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     * })
     */
    private $idUsuario;


}

