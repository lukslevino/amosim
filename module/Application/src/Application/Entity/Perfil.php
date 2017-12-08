<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbPerfil
 *
 * @ORM\Table(name="tb_perfil")
 * @ORM\Entity
 */
class Perfil
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_perfil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPerfil;

    /**
     * @var string
     *
     * @ORM\Column(name="no_perfil", type="string", length=45, nullable=false)
     */
    private $noPerfil;


}

