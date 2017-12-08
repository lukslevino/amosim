<?php

namespace Application\Utils;

use Zend\I18n\Filter\Alnum;

/**
 * Class Cep
 * @package Application\Utils
 */
class Cep
{
    /**
     * @param $cep
     * @return string
     */
    public function inserirMascara($cep)
    {
        $cep = $this->retirarMascara($cep);

        if (strlen($cep) === 8) {
            return substr($cep, 0, 2) . '.' .
            substr($cep, 2, 3) . '-' .
            substr($cep, 4, 3);
        } else {
            return '-';
        }
    }

    /**
     * @param $cep
     * @return array|string
     */
    public function retirarMascara($cep)
    {
        $filter = new Alnum();
        return $filter->filter($cep);
    }
}
