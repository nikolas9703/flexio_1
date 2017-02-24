<?php
namespace Flexio\Modulo\Consumos\Models;

use \Illuminate\Database\Eloquent\Model as Model;

//utilities
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Consumos extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['referencia','prefijo','numero','uuid_bodega','uuid_colaborador','uuid_centro','estado_id','created_by','empresa_id','comentarios'];

    protected $table        = 'cons_consumos';
    protected $fillable     = ['uuid_consumo', 'referencia','prefijo','numero','uuid_bodega','uuid_colaborador','uuid_centro','estado_id','created_by','empresa_id','comentarios'];
    protected $guarded      = ['id'];
    public $timestamps      = true;
    private $prefijo        = "CONS";

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
    }
    public function comp_numeroDocumento()
    {
        return $this->prefijo.$this->numero;
    }

    public function getNumeroAttribute($value)
    {
        return sprintf('%08d', $value);
    }

    public function getUuidConsumoAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidAttribute()
    {
        return $this->uuid_consumo;
    }
    public function getNumeroDocumentoAttribute()
    {
        return $this->prefijo.$this->numero;
    }
    public function getNumeroDocumentoEnlaceAttribute()
    {
        $attrs = [
            "href"  => $this->enlace,
            "class" => "link"
        ];

        $html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
        return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
    }
    public function getEnlaceAttribute()
    {
        return base_url("consumos/ver/".$this->uuid_consumo);
    }
    public function getTimelineAttribute()
    {
        return [
            "Colaborador: ".$this->colaborador->nombre." ".$this->colaborador->apellido,
            "Centro contable: ".$this->centro->nombre,
            "Fecha: ".$this->created_at,
        ];
    }
    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
    public function getTipoSpanAttribute()
    {
        $attrs  = [
            "style" => "float:right;color:#0070BA;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());

        return $html->setType("htmlSpan")->setAttrs($attrs)->setHtml("Consumo")->getSalida();
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
    public function getDiaMesAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->formatLocalized('%d de %B');
    }
    public function getUbicacionAttribute()
    {
        return $this->bodega;
    }
    public function destino() {
        return $this->belongsTo('Flexio\Modulo\Colaboradores\Models\Colaboradores', "uuid_colaborador", "uuid_colaborador");
    }
    public function centro() {
        return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', "uuid_centro", "uuid_centro");
    }
    public function colaborador() {
        return $this->belongsTo('Flexio\Modulo\Colaboradores\Models\Colaboradores', "uuid_colaborador", "uuid_colaborador");
    }

    public function externo()
    {
        return $this->belongsTo('Flexio\Modulo\Colaboradores\Models\Colaboradores', "uuid_colaborador", "uuid_colaborador");
    }

    public function getModuloAttribute()
    {
        return 'Consumo';
    }

    public function getEdadAttribute()
    {
        return Carbon::createFromFormat("d-m-Y", $this->created_at)->diffForHumans();
    }

    public function getFechaHoraAttribute()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at)->format('d/m/Y @ H:i');
    }

    public function lines_items()
    {
        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems', 'tipoable');
    }

    public function origen() {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', "uuid_bodega", "uuid_bodega");
    }
    public function bodega() {
        return $this->belongsTo('Flexio\Modulo\Bodegas\Models\Bodegas', "uuid_bodega", "uuid_bodega");
    }
    public function items() {
        return $this->morphToMany('Flexio\Modulo\Inventarios\Models\Items', 'tipoable', 'lines_items', 'tipoable_id', 'item_id')
            ->withPivot('id', 'uuid_line_item', 'categoria_id', 'empresa_id', 'cantidad', 'unidad_id', 'precio_unidad', 'impuesto_id', 'descuento', 'cuenta_id', 'precio_total', 'impuesto_total', 'descuento_total', 'observacion', 'cantidad2');
    }
    public function items2() {
        return $this->items();
    }

    public function consumos_items(){

        return $this->morphMany('Flexio\Modulo\Inventarios\Models\LinesItems','tipoable');

    }

    //scopes
    public function scopeDeEmpresa($qurey, $empresa_id)
    {
        return $qurey->where("empresa_id", $empresa_id);
    }

    //Mostrar Comentarios
    public function comentario_timeline() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Consumos\\Models\\Consumos2');
    }
    //functiones para el landing_page
    public function landing_comments() {
        return $this->hasMany(Comentario::class,'comentable_id')->where('comentable_type','Flexio\\Modulo\\Consumos\\Models\\Consumos2');
    }

}
