<?php
namespace Flexio\Modulo\Contabilidad\Exportar\Csv;

use Flexio\Library\Util\FormRequest;
use League\Csv\Writer as Writer;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Library\Util\FormatoMoneda;


class HistorialTransaccionCsv {

    public $request;

    function crearCsv($condicion){
        $clause = FormRequest::data_formulario($condicion);
        $historial = AsientoContable::filtro($clause)->get();
        if(is_null($historial))return null;

        $csvdata = [];
        $i=1;
        foreach ($historial as $row)
        {
            $csvdata[$i]['no_transaccion'] = empty($row->codigo) ? utf8_decode($row->nombre) : $row->codigo;
            $csvdata[$i]["fecha"] = $row->created_at;
            $csvdata[$i]["centro_contable"] = $row->nombre_centro_contable;
            $csvdata[$i]["transaccion"] = utf8_decode($row->nombre);
            $csvdata[$i]["Debito"] = FormatoMoneda::numero($row->debito);
            $csvdata[$i]["Credito"] = FormatoMoneda::numero($row->credito);
            $i++;
        }

        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne([
            utf8_decode("No. Transacción"),
            'Fecha',
            'Centro contable',
            utf8_decode("Transacción"),
            utf8_decode('Débito'),
            utf8_decode('Crédito'),
        ]);
        $csv->insertAll($csvdata);
        return $csv;
    }

}
