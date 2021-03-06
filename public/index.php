<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * This makes our life easier when dealing with paths.
 * Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

define('APPLICATION_PATH', __DIR__ . '/..');

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER ['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();

function debug($mixExpression, $boolExit = true, $boolFinish = null)
{
    ?>
    <!DOCTYPE HTML>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="estilo.css">
        <title></title>
    </head>
    <body>
    <?php
    static $arrMessages;
    if (!$arrMessages) {
        $arrMessages = [];
    }

    if ($boolFinish) {
        return (implode(" <br/> ", $arrMessages));
    }

    $arrBacktrace = debug_backtrace();
    $strMessage = "";
    $strMessage .= "<fieldset><legend><font color=\"#007000\">debug</font></legend><pre>";
    foreach ($arrBacktrace[0] as $strAttribute => $mixValue) {
        if ($strAttribute == 'args') continue;
        $strMessage .= "<b>" . $strAttribute . "</b> " . $mixValue . "\n";
    }
    $strMessage .= "<hr />";

    # Abre o buffer, impedindo que seja impresso na tela alguma coisa
    ob_start();
    var_dump($mixExpression);
    # Pega todo o buffer
    $strMessage .= ob_get_clean();

    $strMessage .= "</pre></fieldset>";


    foreach ($arrMessages as $messages) {
        print $messages;
        ob_flush();
        flush();
    }
    print $strMessage;
    $aTrace = debug_backtrace();
    echo $aTrace[0]['file'] . " - Linha: (" . $aTrace[0]['line'] . ")";
    print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";

    if ($boolExit) {
        exit();
    }
}

function dumpd($var, $exit = 1) {
    echo "<pre>";
    \Doctrine\Common\Util\Debug::dump($var);
    $aTrace = debug_backtrace();
    echo $aTrace[0]['file'] . " - Linha: (" . $aTrace[0]['line'] . ")";
    echo '</pre>';
    if ($exit) die;
}
