<?php
namespace Flexio\Modulo\Acreedores\Repository;

use Flexio\Modulo\Acreedores\Models\Acreedores as Acreedores;
use Flexio\Modulo\Acreedores\Models\Acreedores_cat as Acreedores_cat;
use Flexio\Modulo\Acreedores\Models\AcreedoresCategorias as AcreedoresCategorias;
use Flexio\Modulo\Comentario\Models\Comentario;

use Illuminate\Database\Capsule\Manager as Capsule;

class AcreedoresRepository implements AcreedoresInterface{

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $acreedores = Acreedores::deEmpresa($clause["empresa_id"])->siAcreedor();

        //filtros
        $this->_filtros($acreedores, $clause);

        if($sidx!=NULL && $sord!=NULL){$acreedores->orderBy($sidx, $sord);}
        if($limit!=NULL){$acreedores->skip($start)->take($limit);}
        return $acreedores->get();
    }

    public function count($clause = array())
    {
        $acreedores = Acreedores::deEmpresa($clause["empresa_id"])->siAcreedor();

        //filtros
        $this->_filtros($acreedores, $clause);

        return $acreedores->count();
    }

    private function _filtros($acreedores, $clause)
    {
        if(isset($clause["nombre"]) and !empty($clause["nombre"])){$acreedores->deNombre($clause["nombre"]);}
        if(isset($clause["tipo"]) and !empty($clause["tipo"])){$acreedores->deTipo($clause["tipo"]);}
        if(isset($clause["telefono"]) and !empty($clause["telefono"])){$acreedores->deTelefono($clause["telefono"]);}

        $acreedor_uuid = !empty($clause["acreedor"]) ? $clause["acreedor"] : array();
        if(!empty($acreedor_uuid)){
        	$acreedor_uuid = (!empty($acreedor_uuid) ? array_map(function($acreedor_uuid){ return hex2bin($acreedor_uuid); }, $acreedor_uuid) : "");
        	$acreedores->whereIn("uuid_proveedor", $acreedor_uuid);
        }
    }
    // funcion Obsoleto
    public function getTotalAPagar($totalAPagar) {
        return '<label style="border: #d9534f solid 2px;color: #d9534f;display: block;text-align: center;padding: 2px;">$'.number_format($totalAPagar,2).'</label>';
    }

    public function getTipos() {
        return Acreedores_cat::tipos()->get();
    }

    private function _create($usuario_id, $empresa_id, $post)
    {
        $aux = Acreedores::where("ruc", $post["campo"]["ruc"])->first();
        if(count($aux)){return $aux;}

        $acreedor                   = new Acreedores;
        $acreedor->uuid_proveedor   = Capsule::raw("ORDER_UUID(uuid())");
        $acreedor->fecha_creacion   = date("Y-m-d", time());
        $acreedor->creado_por       = $usuario_id;
        $acreedor->id_empresa       = $empresa_id;

        return $acreedor;
    }

    private function _set($acreedor, $campo)
    {
        $acreedor->nombre           = $campo["nombre"];
        $acreedor->telefono         = $campo["telefono"];
        $acreedor->email            = $campo["email"];
        $acreedor->tipo_id          = $campo["tipo"];
        $acreedor->direccion        = $campo["direccion"];
        $acreedor->ruc              = $campo["ruc"];
        $acreedor->id_forma_pago    = $campo["forma_pago"];
        $acreedor->termino_pago_id  = $campo["termino_pago_id"];
        $acreedor->id_banco         = $campo["banco"];
        $acreedor->id_tipo_cuenta   = $campo["tipo_cuenta"];
        $acreedor->numero_cuenta    = $campo["numero_cuenta"];
        $acreedor->limite_credito   = $campo["limite_credito"];
        $acreedor->acreedor         = $campo["acreedor"];
        $acreedor->referencia       = !empty($campo["identificacion"]) ? json_encode($campo["identificacion"]) : "";

        $acreedor->save();
    }

    private function _setCategorias($acreedor, $campo)
    {
        $acreedor->categorias()->sync($campo["categorias"]);
    }

    public function save($post, $usuario_id, $empresa_id)
    {
        if(empty($post))
        {
            die("El metodo save requiere una coleccion de datos");
        }

        $campo = $post["campo"];
        $acreedor = (isset($campo["id"]) and !empty($campo["id"])) ? Acreedores::find($campo["id"]) : $this->_create($usuario_id, $empresa_id, $post);

        //seteo los datos generales del acreedor/proveedor
        $this->_set($acreedor, $campo);

        //seteo las categorias
        $this->_setCategorias($acreedor, $campo);
    }

    public function getAcreedoresCategorias($empresa_id) {
        return AcreedoresCategorias::deEmpresa($empresa_id)->get();
    }

    public function findByUuid($uuid) {
        $acreedor = Acreedores::where("uuid_proveedor", hex2bin($uuid))->first();

        return $acreedor;
    }

    public function find($acreedor_id) {
        $acreedor = Acreedores::find($acreedor_id);

        return $acreedor;
    }
    public function id($id) {
        $acreedor = Acreedores::where("id","=",$id)->first();

        return $acreedor;
    }

    public function getColletionCampos($acreedor)
    {
        return [
            "id"                => $acreedor->id,
            "nombre"            => $acreedor->nombre,
            "telefono"          => $acreedor->telefono,
            "email"             => $acreedor->email,
            "tipo"              => $acreedor->tipo_id,
            "direccion"         => $acreedor->direccion,
            "ruc"               => $acreedor->ruc,
            "forma_pago"        => $acreedor->id_forma_pago,
            "termino_pago_id"   => $acreedor->termino_pago_id,
            "banco"             => $acreedor->id_banco,
            "tipo_cuenta"       => $acreedor->id_tipo_cuenta,
            "numero_cuenta"     => $acreedor->numero_cuenta,
            "limite_credito"    => $acreedor->limite_credito,
            "acreedor"          => $acreedor->acreedor,
            "categorias"        => $this->_getIds($acreedor->categorias),
            "referencia"        => json_decode($acreedor->referencia),
        ];
    }

    private function _getIds($colletion)
    {
        $ids = [];

        foreach($colletion as $row)
        {
            $ids[] = $row->id;
        }

        return $ids;
    }

    static function listar_reporte($uuid_proveedor)
    {
        //dd($uuid_proveedor);
        $query = Acreedores::with(array('descuentos', 'tipo' => function($query){
		}));
        $query->where('uuid_proveedor', hex2bin($uuid_proveedor));
        return $query->get();

    }

    function agregarComentario($id, $comentarios) {
        $acreedor = Acreedores::find($id);
        $comentario = new Comentario($comentarios);
        $acreedor->comentario_timeline()->save($comentario);
        return $acreedor;
    }
}
