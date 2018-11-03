<?php

namespace Couts;

class Model
{
    private $values = []; //para todos os valores vindo do banco

    public function __call($name, $args) //args = valor passado para o atributo
    {
        $method = substr($name, 0, 3); //pegando o name do metodo (get ou set)
        $fieldName = substr($name, 3, strlen($name)); //pegando o resto do nome do metodo a partir da posição 3

        switch ($method) {
            case "get":
                return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : null;
                break;
            case "set":
                $this->values[$fieldName] = $args[0];
                break;
        }
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set" . $key}($value);
        }
    }

    public function getValues()
    {
        return $this->values;
    }
}