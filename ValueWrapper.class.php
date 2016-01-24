<?php
class ValueWrapper extends ValueWrapperRef {

    public function __construct($value = null) {
        $this->value = $value;
        parent::__construct($value);
    }

    private $value;
}
?>
