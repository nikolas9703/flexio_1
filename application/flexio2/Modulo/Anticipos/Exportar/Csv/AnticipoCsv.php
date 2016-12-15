<?php
namespace Flexio\Modulo\Anticipos\Exportar\Csv;

use Flexio\Library\Util\FormRequest;
use League\Csv\Writer as Writer;
use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Library\Util\FormatoMoneda;


class AnticipoCsv {

    function crearCsv($condicion){

        $anticipos = Anticipo::where(function($query) use($condicion){
            $query->where('empresa_id',$condicion['empresa_id']);
            if(isset($condicion['id']))$query->whereIn('id',$condicion['id']);
        })->get();

        if(is_null($anticipos))return null;
        $csvdata = [];
        $i=1;
        foreach ($anticipos AS $row)
        {
            $csvdata[$i]['No. Anticipo'] = $row->codigo;
            $csvdata[$i]["Proveedor"] = utf8_decode($row->anticipable->nombre);
            $csvdata[$i]["Fecha de Anticipo"] = $row->fecha_anticipo;
            $csvdata[$i]["Monto total"] = FormatoMoneda::numero($row->monto);
            $csvdata[$i]["No. Documento"] = $row->numero_documento;
            $csvdata[$i]["Metodo de anticipo"] = $row->catalogo_anticipo->valor;
            $csvdata[$i]["Estado"] = $row->catalogo_estado->valor;
            $i++;
        }

        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne([
            'No. Anticipo',
            'Proveedor',
            'Fecha de Anticipo',
            'Monto total',
            'No. Documento',
            'Metodo de anticipo',
            'Estado',
        ]);
        $csv->insertAll($csvdata);
        return $csv;
    }
}
