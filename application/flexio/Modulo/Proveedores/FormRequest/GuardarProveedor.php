<?php

namespace Flexio\Modulo\Proveedores\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

//models
use Flexio\Modulo\Proveedores\Models\Proveedores;

//Transformers
use Flexio\Modulo\Proveedores\Transformers\ToDatabase;

class GuardarProveedor
{
    protected $request;
    protected $session;
    protected $ToDatabase;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession();
        $this->ToDatabase = new ToDatabase();
    }

    public function save()
    {
        $proveedor = FormRequest::data_formulario($this->request->input('campo'));
        $proveedor = $this->ToDatabase->campo($proveedor);

        if (isset($proveedor['uuid']) && !empty($proveedor['uuid'])){
            return $this->actualizar($proveedor);
        }

        $proveedor['id_empresa'] = $this->session->empresaId();
        $proveedor['creado_por'] = $this->session->usuarioId();
        $proveedor['fecha_creacion'] = Carbon::now();

        return $this->crear($proveedor);
    }

    public function crear($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $this->create_validations($campos);
            $proveedor = Proveedores::create($campos);
            $proveedor->formasDePago()->sync($campos["forma_pago"]);
            $proveedor->categorias()->sync($campos["categorias"]);

            return $proveedor;
        });
    }

    public function actualizar($campos)
    {
        return Capsule::transaction(function () use ($campos) {
            $this->update_validations($campos);
            $proveedor = Proveedores::where('uuid_proveedor', hex2bin($campos['uuid']))->first();
            $proveedor->update($campos);
            $proveedor->formasDePago()->sync($campos["forma_pago"]);
            $proveedor->categorias()->sync($campos["categorias"]);

            return $proveedor;
        });
    }

    private function create_validations($campos)
    {
        $ruc = Proveedores::where('ruc', $campos['ruc'])->first();
        if(count($ruc))throw new \Exception('Actualmente existe un proveedor con el mismo numero de identifiaci&oacute;n');
    }

    private function update_validations($campos)
    {
        $ruc = Proveedores::where(function($q) use ($campos){
            $q->where('ruc', $campos['ruc']);
            $q->where('uuid_proveedor', '!=', hex2bin($campos['uuid']));
        })->first();
        if(count($ruc))throw new \Exception('Actualmente existe un proveedor con el mismo numero de identifiaci&oacute;n');
    }



}
