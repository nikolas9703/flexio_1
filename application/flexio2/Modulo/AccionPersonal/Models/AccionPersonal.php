<?php
namespace Flexio\Modulo\AccionPersonal\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class AccionPersonal extends  Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['empresa_id', 'accionable_id', 'accionable_type', 'no_accion', 'colaborador_id', 'centro_contable_id', 'departamento_id', 'cargo_id', 'nombre_completo', 'centro_contable', 'departamento', 'cargo', 'cedula'];

    protected $table        = 'ap_acciones_personal';
    protected $fillable     = ['empresa_id', 'accionable_id', 'accionable_type', 'no_accion', 'colaborador_id', 'centro_contable_id', 'departamento_id', 'cargo_id', 'nombre_completo', 'centro_contable', 'departamento', 'cargo', 'cedula'];
    protected $guarded      = ['id'];
    protected $appends      = ['icono','codigo','enlace'];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        $tipo_accion = strtolower(str_replace("_trabajo", "", str_replace("_orm", "", $this->accionable_type)));
        return base_url("accion_personal/crear/".$tipo_accion);
    }
    public function getIconoAttribute(){
        return 'fa fa-users';
    }
    public function getCodigoAttribute(){
        return $this->no_accion;
    }
}