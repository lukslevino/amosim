<?php

namespace MySDK\Service;

class Exception extends \Exception {

    protected $arrMessage;
    protected $arrData;

    public function __construct($arrMessage = array(), $arrData, $message = "Exception Service", $code = 0, Exception $previous = null) {
        parent::__construct('Exception Service', $code, $previous);
        $this->setArrMessage($arrMessage)
                ->setArrData($arrData);
    }

    public function getArrMessage() {
        return $this->arrMessage;
    }

    public function getArrData() {
        return $this->arrData;
    }

    public function setArrMessage($arrMessage) {
        $this->arrMessage = $arrMessage;
        return $this;
    }

    public function setArrData($arrData) {
        $this->arrData = $arrData;
        return $this;
    }

}
