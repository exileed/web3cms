<?php
/**
 * Manage Variable.
 * Is overloading class.
 * Storage of variable-value pairs to share between classes and for using in the views.
 */
class MVariable
{
    /**
     * @var array of variables
     */
    private $data;

    /**
     * Get a variable. Overloading.
     * @param string $name of variable
     * @return mixed value of variable
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Whether variable is set. Overloading.
     * @param string $name of variable
     * @return bool
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * Set a variable. Overloading.
     * @param string $name of variable
     * @param mixed $value of variable
     */
    public function __set($name,$value)
    {
        $this->data[$name]=$value;
    }
 
    /**
     * Unset a variable. Overloading.
     * @param string $name of variable
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }
}