<?php

/**
 * <strong>Warning:</strong>
 * Functions that receive values as references function should not be used if setParent is true, because there is no
 * guarantees that after changing the value of the reference, the parent will store the defined value
 * (ex: A wrapper for FlintStone DB will not set the defined value if the reference is changed.)
 */
class DataWrapper extends Wrapper {

    public function __construct(&$parent, $key, $setParent = false) {
        $this->parent = $parent;
        $this->key = $key;
        $this->setParent = $setParent;
    }

    public function &getValue() {
        return $this->parent->get($this->key);
    }

    public function setValue($value) {
        static::unwrapValue($value);
        $this->parent->set($this->key, $value);
        return $this;
    }

    public function setValueRef(&$value) {
        static::unwrapValue($value);
        $this->parent->setRef($this->key, $value);
        return $this;
    }

    public function getWrapped($key, $create = true, $setParent = false) {
        return parent::getWrapped($key, $create, $this->setParent || $setParent);
    }

    private $setParent;
    private $parent;
    private $key;
}
?>
