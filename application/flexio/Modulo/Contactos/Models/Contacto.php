<?php

namespace Flexio\Modulo\Contactos\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Contacto extends Model
{
    protected $table = 'con_contactos';
    protected $fillable = ['nombre', 'empresa_id', 'telefono', 'correo', 'cargo', 'direccion', 'comentario', 'celular', 'cliente_id', 'id_aseguradora', 'tipo_identificacion', 'identificacion', 'provincia', 'letra', 'tomo', 'asiento', 'pasaporte', 'detalle_identificacion'];
    protected $guarded = ['id', 'uuid_contacto'];

    protected $casts = [
        "detalle_identificacion" => "array"
    ];

    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_contacto' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function getUuidContactoAttribute($value)
    {
        return strtoupper(bin2hex($value));
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

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\Contactos\Services\ContactoFilters;
        return $queryFilter->apply($query, $campo);
    }

}
