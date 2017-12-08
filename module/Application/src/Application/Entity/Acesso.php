<?php
namespace Application\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * TbAcesso
 *
 * @ORM\Table(name="tb_acesso", indexes={@ORM\Index(name="fk_tb_acesso_tb_usuario1_idx", columns={"id_usuario"}), @ORM\Index(name="fk_tb_acesso_tb_perfil1_idx", columns={"id_perfil"})})
 * @ORM\Entity
 */
class Acesso
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_acesso", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAcesso;

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
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id_usuario")
     * })
     */
    private $idUsuario;


}

