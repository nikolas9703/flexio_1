<?php

namespace Flexio\Modulo\EntradaManuales\TransformData;

use Flexio\Modulo\EntradaManuales\Models\AsientoContable;

class TransformTransaccion
{
    public function crearInstancia($linesItems)
    {
        $model = [];
        foreach ($linesItems as $item) {
            if (isset($item['id'])) {
                array_push($model, $this->setData($item));
            } else {
                array_push($model, new AsientoContable($item));
            }
        }

        return $model;
    }

    public function setData($item)
    {
        $line = AsientoContable::find($item['id']);
        foreach ($item as $key => $value) {
            if ($key != 'id') {
                $line->{$key} = $value;
            }
        }

        return $line;
    }
}
