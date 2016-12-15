<?php
namespace Flexio\Modulo\Ajustes\Models;

use \Illuminate\Database\Eloquent\Model as Model;

//utilities
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Ajustes extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['uuid_ajuste', 'uuid_bodega', 'numero', 'descripcion', 'tipo_ajuste_id', 'estado_id', 'created_by', 'comentarios', 'total', 'empresa_id', 'uuid_centro', 'razon_id'];

    protected $prefijo      = 'AJS';
    protected $table        = 'aju_ajustes';
    protected $fillable     = ['uuid_ajuste', 'uuid_bodega', 'numero', 'descripcion', 'tipo_ajuste_id', 'estado_id', 'created_by', 'comentarios', 'total', 'empresa_id', 'uuid_centro', 'razon_id'];
    protected $guarded      = ['id'];
    public $timestamps      = true;

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    //buscadores
    public static function findByUuid($uuid_ajuste){
        return self::where("uuid_ajuste", hex2bin($uuid_ajuste))->first();
    }

    //comps
    public function comp_numeroDocumento()
    {
        return $this->numero_documento;
    }

    //GETS
    public function getUuidAjusteAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidAttribute()
    {
        return $this->uuid_ajuste;
    }
    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }
    public function getNumeroDocumentoAttribute(){

        return $this->prefijo.$this->numero;
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
        return '<a href="'.$this->enlace.'" class="link">'.$this->numero_documento.'</a>';
    }

    public function getTipoSpanAttribute()
    {
        $attrs  = [
            "style" => "float:right;color:silver;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("Ajuste")->getSalida();
    }
    public function getTimelineAttribute()
    {
        return [
            "Bodega: ".$this->bodega->nombre,
            "Centro contable: ".$this->centro_contable->nombre,
            "Fecha: ".$this->created_at
        ];
    }
    public function getTipoFaAttribute()
    {
        $attrs = [
            "class" => "fa fa-cubes",
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlI")->setAttrs($attrs)->setHtml('')->getSalida();
    }
    public function getTimeAgoAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->diffForHumans();
    }

    public function getFechaCreacionAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->format('d-m-Y');
    }

    public function getModuloAttribute()
    {
        return 'Ajuste';//mod series
    }

    public function getEdadAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->diffForHumans();
    }

    public function getFechaHoraAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at)->format('d/m/Y @ H:i');
    }

    public function getDiaMesAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->formatLocalized('%d de %B');
    }
    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
    public function getUbicacionAttribute()
    {
        return $this->bodega;
    }
    public function getEnlaceAttribute()
    {
        return base_url("ajustes/ver/".$this->uuid_ajuste);
    }

    public function getUltimoMovimientoNombreAttribute()
    {
        return $this->tipo_ajuste_id == 1 ? 'Ajuste negativo' : 'Ajuste positivo';
    }

    //SCOPES
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    //aplica solo cuando es un ajuste negativo
    public function getOrigenAttribute() {
        return $this->bodega;
    }

    //RELATIONS
    public function centro_contable()
    {
        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', "centro_id", "id");
    }

    public function bodega()
    {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', "uuid_bodega", "uuid_bodega");
    }

    public function items()
    {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'tipoable', 'lines_items', 'tipoable_id', 'item_id')
            ->withPivot('id', 'uuid_line_item', 'categoria_id', 'empresa_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'descuento', 'cuenta_id', 'precio_total', 'impuesto_total', 'descuento_total', 'observacion', 'cantidad2');
    }

    public function ajustes_items(){

        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems','tipoable');

    }

    public function razon()
    {
        return $this->belongsTo('Flexio\Modulo\Ajustes\Models\AjustesRazones', 'razon_id');
    }

    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Ajustes\\Models\\Ajustes2');
    }
    //functiones para el landing_page
    public function landing_comments() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Ajustes\\Models\\Ajustes2');
    }

}
