<?php

namespace Application\Utils;

/**
 * Class Data
 * @package Application\Utils
 */
class Data
{
    /**
     * @param \DateTime $data
     * @param string $formato
     * @return string
     */
    public static function formatar(\DateTime $data, $formato = 'DD/MM/YYYY')
    {
        $datetype = \IntlDateFormatter::FULL;
        $timetype = \IntlDateFormatter::NONE;
        $timezone = 'America/Sao_Paulo';
        $calendar = \IntlDateFormatter::GREGORIAN;

        return \IntlDateFormatter::create('pt_BR', $datetype, $timetype, $timezone, $calendar, $formato)->format($data);
    }
}
