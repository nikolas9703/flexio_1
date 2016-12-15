<?php
namespace Flexio\Modulo\ClientesPotenciales\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;
use Flexio\Modulo\ClientesPotenciales\Models\Correos;
use Flexio\Modulo\ClientesPotenciales\Models\Telefonos;


class ClientesPotenciales extends Model
{
    protected $table        = 'cp_clientes_potenciales';
    protected $fillable     = ['uuid_cliente_potencial','nombre','telefono','correo','empresa_id','compania','id_cargo','id_toma_contacto','descripcion_toma_contacto','referido_por','comentarios','fecha_creacion','creado_por','deleted_at','estado'];
    protected $guarded      = ['uuid_cliente_potencial'];
    protected $primaryKey   = "id_cliente_potencial";
    public $timestamps      = false;
    protected $appends = ['icono','codigo','enlace'];


    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_cliente_potencial' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }

    public function telefonos_asignados() {
        return $this->hasMany(Telefonos::class,'id_cliente_potencial');
    }
    public function correos_asignados() {
        return $this->hasMany(Correos::class,'id_cliente_potencial');
    }

    public function scopeDeTelefonos($query, $findTelefono) {
      return  $query->whereHas("telefonos_asignados", function($telefonos)  use ($findTelefono) {
              // $telefonos->where("telefono", $findTelefono);
              $telefonos->where("telefono","like", "%$findTelefono%");
       });

    }
     public function scopeDeCorreos($query, $findCorreo) {
      return  $query->whereHas("correos_asignados", function($correos)  use ($findCorreo) {
               $correos->where("correo","like", "%$findCorreo%");
       });
     }


    public function toma_contacto() {
        return $this->hasOne('Catalogo_toma_contacto_orm', 'id_cat', 'id_toma_contacto');
    }

    public function getUuidClientePotencialAttribute($value) {

        return strtoupper(bin2hex($value));

    }
    public static function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }
    public static function scopeDeNombre($query, $nombre) {
        return $query->where("nombre",'like',"%$nombre%");
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
    	$attrs = [
    	"href"  => $this->enlace,
    	"class" => "link"
    			];

    	$html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
    	return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->codigo)->getSalida();
    }
    public function getNombreCompletoEnlaceAttribute() {

        $attrs = [
            'href'  => $this->enlace,
            'class' => 'link'
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return $html->setType('htmlA')->setAttrs($attrs)->setHtml($this->nombre)->getSalida();

    }

    public function getEnlaceAttribute() {

        return base_url('clientes_potenciales/editar/'.$this->uuid_cliente_potencial);

    }
	public function comentario_timeline() {
     	    	return $this->morphMany(Comentario::class,'comentable');
    }

    function getCodigoAttribute(){
        return $this->nombre;
    }

    ///functiones del landing page
    public function getIconoAttribute() {
     return 'fa fa-line-chart';
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

}
