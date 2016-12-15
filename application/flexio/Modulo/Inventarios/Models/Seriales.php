<?php
namespace Flexio\Modulo\Inventarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

//services
use Flexio\Modulo\Base\Services\Html as Html;

class Seriales extends Model
{
    protected $table = 'inv_items_seriales';
    protected $fillable = ['uuid_serial', 'nombre', 'descripcion', 'item_id', 'created_by', 'empresa_id', 'estado'];
    protected $guarded = ['id', 'uuid_serial'];
    protected $appends = ['icono','enlace', 'codigo'];
    public $timestamps      = true;

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_serial' => Capsule::raw("ORDER_UUID(uuid())")
        )), true);
        parent::__construct($attributes);
    }

    public function getEnlaceAttribute()
    {
        return base_url("series/ver/".$this->uuid_serial);
    }

    public function getIconoAttribute()
    {
        return 'fa fa-cubes';
    }


    //GETS
    public function getUuidSerialAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getUltimoMovimientoAttribute()
    {
        //ultimo movimiento
        return $this->seriales_lineas->sortBy('line_id')->last()->line_item->tipoable;
    }

    public function getPrimerMovimientoAttribute()
    {
        //ultimo movimiento
        return $this->seriales_lineas->sortBy('line_id')->first()->line_item->tipoable;
    }

    public function getUltimoMovimientoNumeroDocumentoAttribute()
    {
        return $this->ultimo_movimiento->numero_documento;
    }

    public function getUltimoMovimientoNumeroDocumentoEnlaceAttribute()
    {
        return $this->ultimo_movimiento->numero_documento_enlace;
    }


    public function getHiddenOptionsAttribute()
    {
        $html = "";

        $html .= '<a href="' . base_url('series/ver/' . $this->uuid_serial) . '" class="btn btn-block btn-outline btn-success">Ver serie</a>';
        $html .= '<a href="' . base_url('inventarios/trazabilidad/' . $this->uuid_serial) . '" class="btn btn-block btn-outline btn-success">Ver bit&aacute;cora</a>';
        $html .= '<a href="' . $this->ultimo_movimiento->enlace . '" class="btn btn-block btn-outline btn-success">Ver &uacute;ltimo movimiento</a>';

        return $html;
    }

    public function getLinkOptionAttribute()
    {
        return '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $this->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
    }

    public function getUltimoMovimientoBtnEnlaceAttribute()
    {
        $attrs = [
            "href"  => $this->ultimo_movimiento->enlace,
            "class" => "btn btn-block btn-outline btn-success"
        ];
        $html = new Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        $html->setType("HtmlA")->setAttrs($attrs)->setHtml("Ver &uacute;ltimo movimiento");
        return $html->getSalida();
    }

    public function getTrazabilidadBtnEnlaceAttribute()
    {
        $attrs = [
            "href"  => $this->enlace,
            "class" => "btn btn-block btn-outline btn-success"
        ];
        $html = new Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        $html->setType("HtmlA")->setAttrs($attrs)->setHtml("Ver trazabilidad");
        return $html->getSalida();
    }

    public function getCodigoAttribute()
    {
        return $this->nombre;
    }

    //version anterior, aun lo dejo para no perder la url del historial
    /*public function getEnlaceAttribute()
    {
        return base_url("inventarios/trazabilidad/".$this->uuid_serial);
    }*/

    public function getUbicacionAttribute()
    {
        return $this->ultimo_movimiento->ubicacion->nombre_codigo_enlace;
    }

    public function getUbicacionIdAttribute()
    {
        return $this->ultimo_movimiento->ubicacion->id;
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->nombre;
    }

    public function getNumeroDocumentoEnlaceAttribute()
    {
        return '<a href="'.base_url("series/ver/".$this->uuid_serial).'">'.$this->nombre.'</a>';
    }

    //RELACIONES
    public function seriales_lineas()
    {
        return $this->hasMany('Flexio\Modulo\Inventarios\Models\SerialesLineas', 'serial_id', 'id');
    }

    public function depreciaciones()
    {
        return $this->hasMany('Flexio\Modulo\DepreciacionActivosFijos\Models\DepreciacionActivoFijoItem', 'serial_id', 'id');
    }

    public function items()
    {
        return $this->belongsTo(Items::class,'item_id');
    }

    public function getAdquisicionAttribute()
    {
        //primer movimiento
        return count($this->seriales_lineas) ? $this->seriales_lineas->sortBy('line_id')->first()->line_item->precio_unidad : 0;
    }

    public function catalogo_estado()
    {
        return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo','estado','etiqueta')->where('tipo','=','estado')->where('modulo', 'series');
    }

    public function getOtrosCostosAttribute()
    {
        return 0;
    }

    public function getDepreciacionAttribute()
    {
        return $this->depreciaciones->sum('dep_depreciaciones_activos_fijos_items.monto_depreciado');
    }

    public function getValorActualAttribute()
    {
        return $this->adquisicion - $this->otros_costos - $this->depreciacion;
    }

    //SCOPES
    public function scopeDeItem($query, $item_id)
    {
        return $query->where("item_id", $item_id);
    }

    public function scopeDeNombreItem($query, $nombre_item)
    {
        return $query->whereHas("items", function($item) use ($nombre_item){
            $item->where(function($q) use ($nombre_item){
                $q->where('inv_items.nombre', 'like', "%$nombre_item%");
                $q->orWhere('inv_items.codigo', 'like', "%$nombre_item%");
            });
        });
    }

    public function scopeDeCategorias($query, $categorias)
    {
        return $query->whereHas("items", function($item) use ($categorias){
            $item->whereHas('categorias', function($categoria) use ($categorias){
                $categoria->whereIn('inv_categorias.id', $categorias);
            });
        });
    }

    public function scopeDeUuid($query, $uuid_serial)
    {
        return $query->where("uuid_serial", hex2bin($uuid_serial));
    }
    public function scopeDeLinea($query, $line_id)
    {
        return $query->whereHas("seriales_lineas", function($q) use($line_id){
            $q->where("line_id", $line_id);
        });
    }

    public static function serial_items($clause){
      return Seriales::with('items.lines_items')->whereHas('items.categoria',function($query) use($clause){
         $query->where('tipo_id', 8);
         $query->where('estado',  1);
         $query->where('empresa_id',$clause['empresa_id']);
         $query->where('id_categoria',$clause['categoria_id']);
       })->leftJoin('inv_items_categorias',function($join) use($clause){
         $join->on('inv_items_categorias.id_item','=','item_id')
         ->where('inv_items_categorias.id_categoria','=',$clause['categoria_id']);
       });
     }

     public function comentario_timeline()
     {
         return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario','comentable');
     }

     public function landing_comments()
     {
         return $this->morphMany('Flexio\Modulo\Comentario\Models\Comentario','comentable');
     }

     public function present()
     {
         return new \Flexio\Modulo\Series\Presenter\SeriePresenter($this);
     }
}
