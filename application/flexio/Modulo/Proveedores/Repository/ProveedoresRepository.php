<?php
namespace Flexio\Modulo\Proveedores\Repository;

use Flexio\Modulo\Proveedores\Models\Proveedores as Proveedores;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Modulo\Proveedores\Models\ProveedoresCatalogos as ProveedoresCat;



use Illuminate\Database\Capsule\Manager as Capsule;

class ProveedoresRepository{



    public function getCollectionProveedores($proveedores){

        return $proveedores->map(function($proveedor){
            return [
                'id' => $proveedor->uuid_proveedor,
                'saldo_pendiente' => $proveedor->saldo_pendiente,
                'credito' => $proveedor->credito,
                'nombre' => $proveedor->nombre,
                'proveedor_id' => $proveedor->id,
                'retiene_impuesto' => $proveedor->retiene_impuesto,
                'estado' => $proveedor->estado
            ];
        });

    }

    public function getCollectionProveedoresPago($proveedores)
    {
        return $proveedores->map(function ($proveedor) {

            return [
                'id' => $proveedor->id,
                'saldo_pendiente' => $proveedor->saldo_pendiente,
                'credito' => $proveedor->credito,
                'nombre' => $proveedor->nombre,
                'proveedor_id' => $proveedor->id,
                'retiene_impuesto' => $proveedor->retiene_impuesto,
                'forma_pago' => $proveedor->forma_de_pago,
                'banco_id' => $proveedor->id_banco,
                'numero_cuenta' => $proveedor->numero_cuenta,
                'pagables' => $proveedor->facturasPorPagar->map(function ($factura) {
                    return [
                        'pagable_id' => $factura->id,
                        'pagable_type' => get_class($factura),
                        'monto_pagado' => 0,
                        'numero_documento' => $factura->codigo,
                        'fecha_emision' => $factura->fecha_desde,
                        'total' => $factura->total,
                        'pagado' => $factura->pagos_aplicados_suma,
                        'saldo' => $factura->saldo
                    ];
                })
            ];
        });
    }


    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {


        $proveedores = Proveedores::deEmpresa($clause["empresa_id"])->where("estado", "!=", "por_aprobar")->where("estado", "!=", "inactivo");

        //filtros
        $this->_filtros($proveedores, $clause);

        if($sidx!=NULL && $sord!=NULL){$proveedores->orderBy($sidx, $sord);}
        if($limit!=NULL){$proveedores->skip($start)->take($limit);}
        return $proveedores->get();
    }

    public function restar_credito($proveedor_id, $monto, $pago = NULL)
    {
        $precision  = 2;
        $proveedor  = Proveedores::find($proveedor_id);

        $proveedor->credito = round($proveedor->credito, $precision) - round($monto, $precision);

        if($proveedor->credito >= "0")
        {
            return $proveedor->save();
        }

        return false;
    }

    public function sumar_credito($proveedor_id, $monto)
    {
        $proveedor  = Proveedores::find($proveedor_id);

        $proveedor->credito += $monto;

        return $proveedor->save();
    }


    public function getCollectionExportar($proveedores)
    {
        $aux = [];

        foreach ($proveedores as $proveedor)
        {
            $aux[] = $this->_getCollectionExportarRow($proveedor);
        }

        return $aux;
    }

    private function _getCollectionExportarRow($proveedor)
    {
        return [
            utf8_decode($proveedor->nombre),
            $proveedor->telefono,
            $proveedor->email,
            utf8_decode($proveedor->categorias->implode("nombre", ", ")),
            count($proveedor->tipo) ? utf8_decode($proveedor->tipo->etiqueta) : '',
            count($proveedor->ordenes_abiertas) ? $proveedor->ordenes_abiertas->count() : 0,
            $proveedor->saldo_pendiente_moneda,
        ];
    }

    private function _filtros($proveedores, $clause)
    {
        if(isset($clause["uuid_proveedores"]) and !empty($clause["uuid_proveedores"])){$proveedores->deUuids($clause["uuid_proveedores"]);}
        if(isset($clause['nombre']) && !empty($clause['nombre']))$proveedores->where("nombre",'like',"%".$clause['nombre']."%");
        //if(isset($clause['estado']) && !empty($clause['estado']))$proveedores->where("estado",'=', $clause['estado']);
    }

    public function find($proveedor_id){

        return Proveedores::find($proveedor_id);

    }
    function findByUuid2($uuid) {
        return Proveedores::where('uuid_proveedor',bin2hex($uuid))->first();
    }
    function findByUuid($uuid) {
        return Proveedores::where('uuid_proveedor',hex2bin($uuid))->first();
    }
    function findByid($id) {
        return Proveedores::where('id', ($id))->get();
    }
    function agregarComentario($id, $comentarios) {
        $proveedor = Proveedores::find($id);
        $comentario = new Comentario($comentarios);
        $proveedor->comentario_timeline()->save($comentario);
        return $proveedor;
    }

    /**
     * @param $datos
     * @return bool
     */
    public function existDNI($datos)
    {
        dd($datos);
        switch ($datos['campo']['tipo_identificacion']) {
            case 'natural':
                $typeValues = $datos['natural'];
                return Proveedores::where('provincia', $typeValues['provincia'])
                    ->where("letra", $typeValues['letra'])
                    ->where("tomo_rollo", $typeValues['tomo'])
                    ->first() != null;
                break;
            case 'juridico':
                $typeValues = $datos['juridico'];
                return Proveedores::where('digito_verificador', $typeValues['verificador'])
                    ->where("asiento_ficha", $typeValues['asiento'])
                    ->where("folio_imagen_doc", $typeValues['folio'])
                    ->where("tomo_rollo", $typeValues['tomo'])
                    ->first() != null;
                break;
            case 'pasaporte':
                return Proveedores::where('pasaporte', $datos['campo']['pasaporte'])->first() != null;
                break;
        }
        return false;
    }
}
