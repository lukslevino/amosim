<?php

namespace MySDK\Form;

use Zend\Form\Form as zForm;
use Zend\Crypt\PublicKey\Rsa\PublicKey;

class Form extends zForm {

    protected $title = null;
    protected $subtitle = null;
    protected $contextPanel = 'panel-default';
    protected $gridFieldClassPrefix = 'col-xs-';
    protected $gridFieldSizeDefault = 2;
    protected $inputSize = '';
    protected $gridSizeRodape = 12;

    public function setMessageError($keyElement, $arrMmessage) {
        $this->isValid = false;
        parent::setMessages(array($keyElement => $arrMmessage));
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getSubtitle() {
        return $this->subtitle;
    }

    public function setContextPanel($context) {
        $this->contextPanel = $context;
        return $this;
    }

    public function getContextPanel() {
        return $this->contextPanel;
    }

    public function setGridFieldClassPrefix($gridFieldClassPrefix) {
        $this->gridFieldClassPrefix = $gridFieldClassPrefix;
        return $this;
    }

    public function getGridFieldClassPrefix() {
        return $this->gridFieldClassPrefix;
    }

    public function setGridFieldSizeDefault($gridFieldSizeDefault) {
        $this->gridFieldSizeDefault = $gridFieldSizeDefault;
        return $this;
    }

    public function getGridFieldSizeDefault() {
        return $this->gridFieldSizeDefault;
    }

    public function setInputSizeDefault($inputSize) {
        $this->inputSize = $inputSize;
        return $this;
    }

    public function getInputSizeDefault() {
        return $this->inputSize;
    }

    public function setGridSizeRodape($gridSizeRodape) {
        $this->gridSizeRodape = $gridSizeRodape;
        return $this;
    }

    public function getGridSizeRodape() {
        return $this->gridSizeRodape;
    }

}
