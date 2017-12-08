<?php

namespace Application\Utils;

use Zend\I18n\Filter\Alnum;

/**
 * Class Cnpj
 * @package Application\Utils
 */
class Cnpj
{
    /**
     * @param $cnpj
     * @return string
     */
    public function inserirMascara($cnpj)
    {
        $cnpj = $this->retirarMascara($cnpj);
        if (strlen($cnpj) === 14) {
            return substr($cnpj, 0, 2) . '.' .
            substr($cnpj, 2, 3) . '.' .
            substr($cnpj, 5, 3) . '/' .
            substr($cnpj, 8, 4) . '-' .
            substr($cnpj, 12, 2);
        } else {
            return '-';
        }
    }

    /**
     * @param $cnpj
     * @return array|string
     */
    public function retirarMascara($cnpj)
    {
        $filter = new Alnum();
        return $filter->filter($cnpj);
    }
}
