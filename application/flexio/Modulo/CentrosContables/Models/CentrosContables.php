<?php
namespace Flexio\Modulo\CentrosContables\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class CentrosContables extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['nombre','descripcion', 'estado', 'empresa_id', 'padre_id'];
    protected $table        = 'cen_centros';
    protected $fillable     = ['uuid_centro','nombre','descripcion', 'estado', 'empresa_id', 'padre_id'];
    protected $guarded      = ['id'];
    public $timestamps      = false;

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }

    public function toArray()
    {
      $array = parent::toArray();
      $array['hijos'] = $this->where('padre_id',$this->id)->count() == 0? true : false;
      return $array;
    }
    //GETS
    public function getUuidCentroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    //SCOPES

    public function scopeDeEmpresa($query, $empresa_id)
    {          $query->where("estado","Activo");
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeTransaccionales($query, $empresa_id)
    {
        $ids = CentrosContables::deEmpresa($empresa_id)->lists('padre_id');

        return $query->whereNotIn("id", $ids);
    }

    public function scopeActiva($query)
    {
        return $query->where("estado", "Activo");
    }

    public function scopeDeFiltro($query, $campo)
    {
        $queryFilter = new \Flexio\Modulo\CentrosContables\Service\CentroContableFilters;
        return $queryFilter->apply($query, $campo);
    }




}
