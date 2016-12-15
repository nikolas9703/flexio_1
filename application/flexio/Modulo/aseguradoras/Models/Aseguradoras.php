<?php
namespace Flexio\Modulo\aseguradoras\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Politicas\PoliticableTrait;

class Aseguradoras extends Model
{
	use PoliticableTrait;
	
	// propiedad de politica
    protected $politica = 'aseguradora';
	
    protected $table        = 'seg_aseguradoras';    
    protected $fillable     = ['uuid_aseguradora', 'nombre', 'ruc', 'telefono', 'email', 'direccion', 'direccion','tomo','folio','asiento','digverificador','descuenta_comision',  'imagen_archivo', 'creado_por', 'created_at', 'update_at', 'uuid_cuenta_pagar','uuid_cuenta_cobrar','empresa_id','estado'];
    protected $guarded      = ['id'];
    
    //scopes
    public function scopeDeEmpresa($query, $empresa_id) {
        return $query->where("empresa_id", $empresa_id);
    }  
	
	public function datosEmpresa() {
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }
	
	public function creadopor() {
        return $this->hasOne(Usuarios::class, 'id', 'creado_por');
    }

    function present(){
        return new \Flexio\Modulo\aseguradoras\Presenter\AseguradorasPresenter($this);
    }
}