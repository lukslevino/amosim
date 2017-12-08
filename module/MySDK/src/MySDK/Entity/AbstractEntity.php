<?php

namespace MySDK\Entity;

use Zend\Stdlib\Hydrator;

class AbstractEntity {

    public function __construct(array $options = array()) {
        (new Hydrator\ClassMethods ())->hydrate($options, $this);
    }

    public function toArray() {
        return (new Hydrator\ClassMethods ())->extract($this);
    }

}
