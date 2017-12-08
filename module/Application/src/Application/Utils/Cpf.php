<?php

namespace Application\Utils;

use Zend\I18n\Filter\Alnum;
use Zend\Validator\AbstractValidator;

/**
 * Class Cpf
 * @package Application\Utils
 */
class Cpf extends AbstractValidator
{

    const NOT_CPF = 'notCpf';

    protected $_messageTemplates = array(
        self::NOT_CPF => "'%value%' não é um CPF válido."
    );

    protected $_numericOnly;

    /**
     * Se o CPF for apenas numerico 12345678909 então $numbersOnly é true
     * Se não, 123.456.789-09
     *
     * @param bool $numbersOnly
     */
    public function __construct($numbersOnly = false, $options = null)
    {
        parent::__construct($options);
        $this->_setNumericOnly($numbersOnly);
    }

    /**
     * Sets $_numericOnly
     *
     * @param bool $bool
     */
    private function _setNumericOnly($bool = false)
    {
        $this->_numericOnly = $bool;
        if ($this->_numericOnly === true) {
            $this->_regexp = '/^(\d){11}$/';
        } else {
            $this->_regexp = '/^(\d){3}(\.\d{3}){2}-(\d){2}$/';
        }
        return $this;
    }

    /**
     *
     * @param mixed $cpf
     * @return boolean
     * @throws Zend_Valid_Exception If validation of $cpf is impossible
     * @see ValidatorInterface::isValid()
     */
    public function isValid($cpf)
    {
        $cpf = $this->inserirMascara($cpf);
        // checks regexp, first and second validation Digit
        if (preg_match($this->_regexp, $cpf) &&
            $this->_checkDigitOne($this->_removeNonDigits($cpf)) &&
            $this->_checkDigitTwo($this->_removeNonDigits($cpf)) &&
            $this->checkNumerosRepetidos($cpf)
        ) {
            return true;
        }
        $this->error(self::NOT_CPF, $cpf);
        return false;
    }

    /**
     * Checa se o cpf informado possui todos os numeros repetidos.
     * Ex: 111.111.111-11
     */
    public function checkNumerosRepetidos($cpf)
    {
        $cpf = $this->retirarMascara($cpf);
        $numerosRepetidos = array(
            '00000000000',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
            '66666666666',
            '77777777777',
            '88888888888',
            '99999999999'
        );
        if (in_array($cpf, $numerosRepetidos)) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $cpf
     * @return bool
     */
    private function _checkDigitOne($cpf)
    {
        $multipliers = array(
            10,
            9,
            8,
            7,
            6,
            5,
            4,
            3,
            2
        );
        return $this->_getDigit($cpf, $multipliers) == $cpf{9};
    }

    /**
     *
     * @param string $cpf
     * @return bool
     */
    private function _checkDigitTwo($cpf)
    {
        $multipliers = array(
            11,
            10,
            9,
            8,
            7,
            6,
            5,
            4,
            3,
            2
        );
        return $this->_getDigit($cpf, $multipliers) == $cpf{10};
    }

    /**
     *
     * @param string $cpf
     * @param array(int) $multipliers
     * @return int
     */
    private function _getDigit($cpf, $multipliers)
    {
        $sum = null;
        foreach ($multipliers as $key => $v) {
            $sum += $cpf{$key} * $v;
        }
        $digit = $sum % 11;
        if ($digit < 2) {
            $digit = 0;
        } else {
            $digit = 11 - $digit;
        }
        return $digit;
    }

    /**
     *
     * @param string $cpf
     * @return string
     */
    private function _removeNonDigits($cpf)
    {
        return preg_replace('/\D/', '', $cpf);
    }


    /**
     * @param $cpf
     * @return string
     */
    public function inserirMascara($cpf)
    {
        $cpf = $this->retirarMascara($cpf);

        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' .
            substr($cpf, 3, 3) . '.' .
            substr($cpf, 6, 3) . '-' .
            substr($cpf, 9, 2);
        } else {
            return '-';
        }
    }

    /**
     * @param $cpf
     * @return array|string
     */
    public function retirarMascara($cpf)
    {
        $filter = new Alnum();
        return $filter->filter($cpf);
    }
}
