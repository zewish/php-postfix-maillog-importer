<?php

namespace Base;

class Config
{
    protected $_settings = array();

    public function __get($name)
    {
        if (array_key_exists($name, $this->_settings)) {
            return $this->_settings[$name];
        }

        $method = '_get' . ucfirst($name);
        if (method_exists(__CLASS__, $method)) {
            return $this->$method;
        }

        throw new ConfigException('Config setting "' . $name . '" not found.');
    }

    public function __set($name, $value)
    {
        $this->_settings[$name] = $value;
    }
}

class ConfigException extends \Exception {}