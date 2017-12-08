<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbPontuacao
 *
 * @ORM\Table(name="tb_pontuacao", indexes={@ORM\Index(name="fk_tb_pontos_tb_usuario1_idx", columns={"id_usuario"})})
 * @ORM\Entity
 */
class Pontuacao
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_pontos", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPontos;

    /**
     * @var integer
     *
     * @ORM\Column(name="pontos", type="integer", nullable=false)
     */
    private $pontos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     * })
     */
    private $idUsuario;


}

