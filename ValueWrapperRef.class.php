<?php
class ValueWrapperRef extends Wrapper {

    public function __construct(&$ref) {
        $this->ref = &$ref;
    }

    public function &getValue() {
        return $this->ref;
    }

    public function setValue($value) {
        $this->unwrapValue($value);
        $this->ref = $value;
        return $this;
    }

    public function setValueRef(&$value) {
        $this->unwrapValue($value);
        $this->ref = &$value;
        return $this;
    }

    private $ref;
}
?>
