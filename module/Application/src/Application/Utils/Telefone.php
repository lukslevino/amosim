<?php

namespace Application\Utils;

use Zend\I18n\Filter\Alnum;

/**
 * Class Telefone
 * @package Application\Utils
 */
class Telefone
{
    /**
     * @param $ddd
     * @param $telefone
     * @return string
     */
    public function inserirMascara($ddd, $telefone)
    {
        $telefone = $this->retirarMascara($telefone);

        if ($ddd && $telefone) {
            return '(' . $ddd . ') ' . substr($telefone, -9, -4) . '-' . substr($telefone, -4);
        } else {
            return '';
        }
    }

    /**
     * @param $telefone
     * @return array|string
     */
    public function retirarMascara($telefone)
    {
        $filter = new Alnum();
        return $filter->filter($telefone);
    }
}
