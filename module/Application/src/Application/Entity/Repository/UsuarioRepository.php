<?php

namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UsuarioRepository extends EntityRepository {

    public function findArray() {
        $users = $this->findAll();
        $a = array();
        foreach ($users as $user) {
            $a[$user->getId()]['id'] = $user->getId();
            $a[$user->getId()]['nome'] = $user->getNome();
            $a[$user->getId()]['email'] = $user->getEmail();
        }

        return $a;
    }

}
