<?php
abstract class Wrapper implements IteratorAggregate {

    public abstract function &getValue();
    public abstract function setValue($value);
    public abstract function setValueRef(&$value);

    public function isNull() {
        return $this->getValue() == null;
    }

    public function isArray() {
        $value = $this->getValue();
        return $value != null && is_array($value);
    }

    public function size() {
        return count($this->getValue());
    }

    public function hasKey($key) {
        $data = &$this->getValue();
        if ($data != null && is_array($data))
            return array_key_exists($key, $data);
        return false;
    }

    public function has($value) {
        $this->unwrapValue($value);
        $data = &$this->getValue();
        if ($data != null && is_array($data))
            return in_array($value, $data);
        return false;
    }

    public function set($key, $value) {
        $this->unwrapValue($value);
        $data = &$this->getValue();
        if ($data == null || !is_array($data)) {
            $data = array();
            $data[$key] = $value;
            $this->setValue($data);
        } else
            $data[$key] = $value;
        return $this;
    }

    public function setRef($key, &$value) {
        $this->unwrapValue($value);
        $data = &$this->getValue();
        if ($data == null || !is_array($data)) {
            $data = array();
            $data[$key] = &$value;
            $this->setValue($data);
        } else
            $data[$key] = &$value;
        return $this;
    }

    public function push($value) {
        $this->unwrapValue($value);
        $data = &$this->getValue();
        if ($data == null || !is_array($data)) {
            $data = array();
            $data[] = $value;
            $this->setValue($data);
        } else
            $data[] = $value;
        return $this;
    }

    public function pushRef(&$value) {
        $this->unwrapValue($value);
        $data = &$this->getValue();
        if ($data == null || !is_array($data)) {
            $data = array();
            $data[] = &$value;
            $this->setValue($data);
        } else
            $data[] = &$value;
        return $this;
    }

    public function &get($key, $default = null) {
        $this->unwrapValue($default);
        $data = &$this->getValue();
        if ($data == null || !array_key_exists($key, $data))
            return $default;
        return $data[$key];
    }

    public function delete($key) {
        $data = &$this->getValue();
        if (is_array($data) && array_key_exists($key, $data))
            unset($data[$key]);
    }

    public function getWrapped($key, $create = true, $setParent = false) {
        if ($create || $this->getValue() != null)
            return new DataWrapper($this, $key, $setParent);
        return null;
    }

    public function setValueComplex($complexKey, $value) {
        return $this->getWrappedComplex($complexKey)->setValue($value);
    }

    public function getWrappedComplex($complexKey, $create = true, $setParent = false) {
        $keys = explode('.', $complexKey);
        return array_reduce($keys, function($obj, $key) use ($create, $setParent) {
            return is_null($obj) ? $obj : $obj->getWrapped($key, $create, $setParent);
        }, $this);
    }

    protected function unwrapValue(&$value) {
        if ($value != null && is_object($value) && is_subclass_of($value, 'Wrapper'))
            $value = $value->getValue();
    }

    /**
     * Modifying the value of the current wrapped object while iterating will result in undefined behaviour.
     */
    public function getIterator() {
        $data = &$this->getValue();
        if ($data != null && is_array($data)) {
            foreach($data as $key => $value)
                yield $key => (new DataWrapper($this, $key));
        }
    }
}
?>
