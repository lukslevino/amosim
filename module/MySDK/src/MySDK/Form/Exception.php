<?php

namespace MySDK\Form;

class Exception extends \Exception {

    private $field;

    public function __construct($message = "", $field = "", $code = 0, \Exception $previous = null) {
        $this->setField($field);
        parent::__construct($message, $code, $previous);
    }

    public function getFieldMessage() {
        return [$this->getField() => [$this->getMessage()]];
    }

    public function getField() {
        return $this->field;
    }

    public function setField($field) {
        $this->field = $field;
        return $this;
    }

}
