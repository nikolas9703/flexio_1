<?php
namespace Flexio\Modulo\OrdenesTrabajo\Models;

use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenesTrabajoCatalogo;
use Flexio\Modulo\OrdenesTrabajo\Models\Servicios;
use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Cotizaciones\Models\LineItem as LineItem;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Talleres\Models\EquipoTrabajo;
use Flexio\Modulo\CentroFacturable\Models\CentroFacturable;
use Flexio\Modulo\Documentos\Models\Documentos;

class OrdenTrabajo extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['cliente_id', 'tipo_orden_id', 'centro_id', 'estado_id', 'orden_de', 'orden_de_id', 'lista_precio_id', 'facturable_id', 'bodega_id', 'fecha_inicio', 'fecha_planificada_fin', 'fecha_real_fin', 'comentarios'];
    protected $revisionFormattedFieldNames = [
      'cliente_id'        => 'Cliente',
      'tipo_orden_id'     => 'Tipo de orden de trabajo',
      'centro_id'         => 'Centro contable',
      'estado_id'         => 'Estado',
      'lista_precio_id'   => 'Lista de precio',
      'facturable_id'     => 'Facturable',
      'bodega_id'         => 'Despacho desde bodega',
      'fecha_inicio'      => 'Fecha de inicio',
      'fecha_planificada_fin' => 'Fecha de planificada de fin',
      'fecha_real_fin'    => 'Fecha real de finalizaci&oacute;n',
      'comentario'        => 'Observaciones'
    ];

    protected $table    = 'odt_ordenes_trabajo';
    protected $fillable = ['uuid_orden_trabajo', 'empresa_id', 'numero', 'cliente_id', 'tipo_orden_id', 'centro_id', 'estado_id', 'lista_precio_id', 'facturable_id', 'bodega_id', 'fecha_inicio', 'fecha_planificada_fin', 'fecha_real_fin', 'comentario', 'creado_por', 'orden_de', 'orden_de_id','equipo_trabajo_id','centro_facturable_id'];
    protected $guarded	= ['id'];
    protected $appends  = ['icono','codigo','enlace'];

    protected static $ci;

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();
        static::updating(function($odt) {
            $cambio = $odt->getDirty();
            $original = $odt->getOriginal();
            if(isset($cambio['estado_id'])){
                $catalogo_anterior = OrdenesTrabajoCatalogo::where("id","=",$original['estado_id'])->get();
                $catalogo_actual = OrdenesTrabajoCatalogo::where("id","=",$cambio['estado_id'])->get();

                $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado en la orden de trabajo</b></br></br>";
                $descripcion .= "Estado actual: ".$catalogo_actual[0]->etiqueta.'</br></br>';
                $descripcion .= "Estado anterior: ".$catalogo_anterior[0]->etiqueta;

                $update = [
                    'codigo' => $odt->numero,
                    'usuario_id' => $odt->creado_por,
                    'empresa_id' => $odt->empresa_id,
                    'odt_id'=> $odt->id,
                    'tipo'   => "actualizado",
                    'descripcion' => $descripcion,
                    'antes' => $catalogo_anterior[0]->id,
                    'despues' => $catalogo_actual[0]->id
                ];
                OrdenesTrabajoHistorial::create($update);
                return $odt;
            }

        });
        static::created(function($odt){
           // dd($odt);

            $create = [
                'codigo' => $odt->codigo,
                'usuario_id' => $odt->creado_por,
                'empresa_id' => $odt->empresa_id,
                'odt_id'=> $odt->id,
                'tipo'   => "creado",
                'descripcion' => "<b style='color:#0080FF; font-size:15px;'>Se creó la orden de trabajo</b></br></br>No. ".$odt->codigo."</br></br>Estado: Por agendar",
                'antes' => '',
                'despues' => 11
            ];
            OrdenesTrabajoHistorial::create($create);
            return $odt;
        });
    }

    /**
     * The morphMany revisions relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function revisions() {
        return $this->morphMany('Venturecraft\Revisionable\Revisionable', 'revisionable');
    }

    public function getUuidOrdenTrabajoAttribute($value) {
    	return strtoupper(bin2hex($value));
    }

    public function empresa() {
    	return $this->hasOne('Empresa_orm', 'id', 'empresa_id');
    }

    public function getClienteNombreAttribute(){
      if (is_null($this->cliente)) {
          return '';
      }
      return $this->cliente->nombre;
    }

    public function cliente() {
    	return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function centro() {
    	return $this->belongsTo('Centros_orm', 'centro_id', 'id');
    }

    public function estado() {
    	return $this->hasOne(OrdenesTrabajoCatalogo::class, 'id', 'estado_id')->where('identificador', '=', 'Estado');
    }
    public function scopeEstadosActivos($query) {
        return $query->whereHas('estado', function ($query) {
            $query->where('identificador', '=', 'Estado')
            ->where('etiqueta', '!=', 'Terminada')
            ->where('etiqueta', '!=', 'Anulada');
        });
    }

    public function facturable() {
    	return $this->hasOne(OrdenesTrabajoCatalogo::class, 'id', 'facturable_id')->where('identificador', '=', 'Facturable');
    }

    public function servicios() {
		return $this->hasMany(Servicios::class, 'orden_id');
	}
	public function facturas(){
		return $this->morphToMany(FacturaVenta::class,'fac_facturable')->withPivot('empresa_id','items_facturados');
	}
	function scopeDeEmpresa($query, $clause){
		return $query->where('empresa_id','=',$clause['empresa_id']);
	}
	function scopeEstadoValido($query){
		return $query->whereHas('estado', function ($query) {
			$query->where('identificador', '=', 'Estado')->where('etiqueta', 'LIKE', '%por facturar%');
		});
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
        return base_url("ordenes_trabajo/ver/".$this->uuid_orden_trabajo);
    }
    public function getIconoAttribute(){
        return 'fa fa-wrench';
    }
    public function getCodigoAttribute(){
        return $this->numero;
    }
    public function items() {
        return $this->morphMany(LineItem::class,'tipoable');
    }

    function cobros(){
        return $this->morphMany('Flexio\Modulo\Cobros\Models\Cobro','empezable');
    }

    public function vendedor() {
  		return $this->belongsTo(Usuarios::class,'creado_por');
  	}
    public function equipoTrabajo() {
    	return $this->hasMany(EquipoTrabajo::class, 'id', 'equipo_trabajo_id');
    }

    public function centro_fact() {
        return $this->hasMany(CentroFacturable::class, 'id', 'centro_facturable_id');
    }
    function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
    }
    public function historial(){
        return $this->hasMany(OrdenesTrabajoHistorial::class,'odt_id');
    }
}
