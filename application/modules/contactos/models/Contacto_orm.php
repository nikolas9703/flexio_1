<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Contacto_orm extends Model
{
    protected $table = 'con_contactos';
    protected $fillable = ['nombre', 'empresa_id', 'telefono', 'correo', 'cargo', 'direccion', 'comentario', 'celular', 'cliente_id', 'id_aseguradora', 'tipo_identificacion', 'identificacion', 'provincia', 'letra', 'tomo', 'asiento', 'pasaporte'];
    protected $guarded = ['id', 'uuid_contacto'];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_contacto' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function toArray() {
        $array = parent::toArray();
        return $array;
    }

    public function getCreatedAtAttribute($date) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }

    public function getUuidContactoAttribute($value) {
        return strtoupper(bin2hex($value));
    }

    static function lista_totales($clause = array()) {
        return self::where(function ($query) use ($clause) {
            $query->where('empresa_id', '=', $clause['empresa_id'])->where('estado', '=', 'activo');
            if (isset($clause['cliente_id'])) $query->where('cliente_id', '=', $clause['cliente_id']);
            else if (isset($clause['id_aseguradora'])) $query->where('id_aseguradora', '=', $clause['id_aseguradora']);
        })->count();
    }

    /**
     * function de listar y busqueda
     */
    public static function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $contactos = self::where(function ($query) use ($clause, $sidx, $sord, $limit, $start) {
            $query->where('empresa_id', '=', $clause['empresa_id'])->where('estado', '=', 'activo');
            if (isset($clause['cliente_id'])) $query->where('cliente_id', '=', $clause['cliente_id']);
            else if (isset($clause['id_aseguradora'])) $query->where('id_aseguradora', '=', $clause['id_aseguradora']);            
            if ($limit != NULL) $query->skip($start)->take($limit);
        });
        if ($sidx != NULL && $sord != NULL) $contactos->orderBy($sidx, $sord);
        return $contactos->get();
    }

    public static function asignar_contacto_principal($campo,$clause = array()) {
        $contactos = self::where($campo, $clause['cliente_id'])->get();
        foreach ($contactos as $cont) {
            if ($cont->id == $clause['id']) {
                $cont->principal = 1;
            } else {
                $cont->principal = 0;
            }
            $cont->save();
        }
        return true;

    }

    public static function contacto_inactivo($id = NULL) {
        $result = self::find($id);
        $result->estado = "inactivo";
        $result->save();
        return true;

    }
}
