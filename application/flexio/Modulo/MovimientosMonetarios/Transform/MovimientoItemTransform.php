<?php

namespace Flexio\Modulo\MovimientosMonetarios\Transform;

class MovimientoItemTransform
{
    protected $scope;//class name, it is not a instance....

    public function __construct($scope)
    {
        $this->setScope($scope);
    }

    public function crearInstancia($filas)
    {
        $model = [];
        foreach ($filas as $fila) {
            $fila = $this->filaTransform($fila);
            if (isset($fila['id']) && !empty($fila['id'])) {
                array_push($model, $this->setData($fila));
            } else {
                array_push($model, new $this->scope($fila));
            }
        }
        return $model;
    }

    public function setData($fila)
    {
        $className = $this->scope;
        $model = $className::find($fila['id']);
        if (count($model) > 0) {
            foreach ($fila as $key => $value) {
                if ($key != 'id') {
                    $model->{$key} = $value;
                }
            }
        }
        return $model;
    }

    public function filaTransform($fila)
    {
        return $fila;
    }

    private function setScope($scope)
    {
        $this->scope = $scope;
    }
}
