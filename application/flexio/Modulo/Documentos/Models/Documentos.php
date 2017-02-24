<?php
namespace Flexio\Modulo\Documentos\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Colaboradores\Models\Colaboradores as Colaboradores;
use Flexio\Modulo\Pedidos\Models\Pedidos as Pedido;
use Flexio\Modulo\OrdenesCompra\Models\OrdenesCompra as OrdenesCompra;
use Flexio\Modulo\OrdenesVentas\Models\OrdenVenta as OrdenVenta;
use Flexio\Modulo\Pagos\Models\Pagos as Pagos;
use Flexio\Modulo\FacturasCompras\Models\FacturaCompra as FacturaCompra;
use Flexio\Modulo\FacturasVentas\Models\FacturaVenta as FacturaVenta;
use Flexio\Modulo\Cliente\Models\Cliente as Cliente;
use Flexio\Modulo\InteresesAsegurados\Models\VehiculoAsegurados as VehiculoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\InteresesPersonas as InteresesPersonas;
use Flexio\Modulo\InteresesAsegurados\Models\AereoAsegurados as AereoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\MaritimoAsegurados as MaritimoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\ProyectoAsegurados as ProyectoAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\CargaAsegurados as CargaAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\ArticuloAsegurados as ArticuloAsegurados;
use Flexio\Modulo\InteresesAsegurados\Models\UbicacionAsegurados as UbicacionAsegurados;
use Flexio\Modulo\Cotizaciones\Models\Cotizacion as Cotizacion;
use Flexio\Modulo\Proveedores\Models\Proveedores as Proveedores;
use Flexio\Modulo\OrdenesAlquiler\Models\OrdenVentaAlquiler;
use Flexio\Modulo\Inventarios\Models\Items as Items;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Talleres\Models\EquipoTrabajo;
use Flexio\Modulo\Cajas\Models\Cajas;
use Flexio\Modulo\OrdenesTrabajo\Models\OrdenesTrabajo;
use Flexio\Modulo\SubContratos\Models\SubContrato;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;
use Flexio\Modulo\Catalogos\Models\Catalogo;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;

class Documentos extends Model
{
    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf =['id', 'modulo_id', 'empresa_id', 'documentable_id', 'documentable_type', 'archivo_ruta', 'archivo_nombre', 'extra_datos', 'subido_por', 'nombre_documento', 'centro_contable_id', 'fecha_documento', 'tipo_id', 'etapa', 'padre_id', 'archivado','uuid_documento'];

	protected $table = 'doc_documentos';
	protected $fillable = ['id', 'modulo_id', 'empresa_id', 'documentable_id', 'documentable_type', 'archivo_ruta', 'archivo_nombre', 'extra_datos','subido_por','nombre_documento', 'centro_contable_id', 'fecha_documento', 'tipo_id', 'etapa', 'padre_id', 'archivado', 'uuid_documento'];
	protected $guarded = ['id', 'uuid_documento'];
    
    public function __construct(array $attributes = array()) {
        $this->setRawAttributes(array_merge($this->attributes, array(
            'uuid_documento' => Capsule::raw("ORDER_UUID(uuid())")
                )), true);
        parent::__construct($attributes);
    }    
     public static function boot() {
        parent::boot();
        
        static::updating(function($documentos) {          
            $cambio = $documentos->getDirty();
            $original = $documentos->getOriginal();
            if(isset($cambio['etapa'])){
            $catalogo_anterior = Catalogo::where("etiqueta","=",$original['etapa'])->get();
            $catalogo_actual = Catalogo::where("etiqueta","=",$cambio['etapa'])->get();      
            $descripcion = "<b style='color:#0080FF; font-size:15px;'>Cambio de estado en el documento</b></br></br>";
            $descripcion .= "Estado actual: ".$catalogo_actual[0]->valor.'</br></br>';
            $descripcion .= "Estado anterior: ".$catalogo_anterior[0]->valor;
              
                $create = [
                    'codigo' => 'ninguno',
                    'usuario_id' => $documentos->subido_por,
                    'empresa_id' => $documentos->empresa_id,
                    'documento_id'=> $documentos->id,
                    'tipo'   => "actualizado",
                    'descripcion' => $descripcion
                ];               
                DocumentosHistorial::create($create);
                return $documentos;
            }

        });
        static::created(function($documentos){
        
        if($documentos->padre_id != 0){
            $relacionado_a = json_decode($documentos->extra_datos);            
            $descripcion = "<b style='color:#0080FF; font-size:15px;'>Actualiz&oacute; documento: " . $documentos->archivo_nombre . "</b></br></br>";            
            $descripcion .= "Factura de compra: " . $relacionado_a->campo->relacionado_a . "<br /><br /><div><a target='_blank' href='". base_url($documentos->archivo_ruta . '/' . $documentos->archivo_nombre) ."' class='btn btn-sm btn-success'> Descargar documento </a></div>";  
                $create = [
                    'codigo' => 'ninguno',
                    'usuario_id' => $documentos->subido_por,
                    'empresa_id' => $documentos->empresa_id,
                    'documento_id'=> $documentos->padre_id,
                    'tipo'   => "documento",
                    'descripcion' => $descripcion
                ];               
          
        }else{
        $catalogo_actual = Catalogo::where("etiqueta","=",$documentos->etapa)->get();        
        $catalogo_name = !empty($catalogo_actual[0]) ? $catalogo_actual[0]->valor : '';

        $create = [
                'codigo' => 'ninguno',
                'usuario_id' => $documentos->subido_por,
                'empresa_id' => $documentos->empresa_id,
                'documento_id'=> $documentos->id,
                'tipo'   => "creado",
                'descripcion' => "<b style='color:#0080FF; font-size:15px;'>Se creó un documento</b></br></br>Estado: " . $catalogo_name . "<br /><br /><div><a target='_blank' href='". base_url(!empty($documentos->archivo_ruta) ? $documentos->archivo_ruta : '' . '/' . !empty($documentos->archivo_nombre) ? $documentos->archivo_nombre : '') ."' class='btn btn-sm btn-success'> Descargar documento </a></div>"
            ];
            
        }    
       
            DocumentosHistorial::create($create);
            return $documentos;
        });
    }   
        
	public function documentable()
    {
        return $this->morphTo();
	}

        public function getNombreDocumentoAttribute($value) {

            if(empty($value)){
                return $this->archivo_nombre;
            }
            return $value;

        }
        
        public function getUuidDocumentoAttribute($value)
        {
        return strtoupper(bin2hex($value));
        }   

	public function colaboradores() {
		return $this->belongsTo(Colaboradores::class, 'documentable_id');
	}

	public function pedidos() {
		return $this->belongsTo(Pedido::class, 'documentable_id');
	}

	public function ordenes_compra() {
		return $this->belongsTo(OrdenesCompra::class, 'documentable_id');
	}

	public function factura_compra() {
		return $this->belongsTo(FacturaCompra::class, 'documentable_id');
	}

	public function factura_venta() {
		return $this->belongsTo(FacturaVenta::class, 'documentable_id');
	}

        public function cliente() {
		return $this->belongsTo(Cliente::class, 'documentable_id');
	}

	public function subido_por()
    {
		return $this->belongsTo(Usuarios::class, 'subido_por');
	}

    public function usuario()
    {
		return $this->belongsTo(Usuarios::class, 'subido_por');
	}

	public function equipo_trabajo() {
		return $this->belongsTo(EquipoTrabajo::class, 'documentable_id');
	}

        public function persona() {
		return $this->belongsTo(InteresesPersonas::class, 'documentable_id');
	}

        public function vehiculo_asegurados() {
		return $this->belongsTo(VehiculoAsegurados::class, 'documentable_id');
	}

        public function casco_aereo() {
		return $this->belongsTo(AereoAsegurados::class, 'documentable_id');
	}

        public function casco_maritimo() {
		return $this->belongsTo(MaritimoAsegurados::class, 'documentable_id');
	}

        public function proyecto() {
		return $this->belongsTo(ProyectoAsegurados::class, 'documentable_id');
	}

        public function carga() {
		return $this->belongsTo(CargaAsegurados::class, 'documentable_id');
	}

        public function articulo() {
            return $this->belongsTo(ArticuloAsegurados::class, 'documentable_id');
        }
        public function ubicacion() {
            return $this->belongsTo(UbicacionAsegurados::class, 'documentable_id');
        }
        public function cotizaciones() {
            return $this->belongsTo(Cotizacion::class, 'documentable_id');
        }
        public function ordenes_ventas() {
            return $this->belongsTo(OrdenVenta::class, 'documentable_id');
        }
				public function ordenes_alquiler() {
            return $this->belongsTo(OrdenVentaAlquiler::class, 'documentable_id');
        }
        public function proveedores() {
            return $this->belongsTo(Proveedores::class, 'documentable_id');
        }
        public function subcontratos() {
            return $this->belongsTo(Subcontrato::class, 'documentable_id');
        }
        public function items() {
            return $this->belongsTo(Items::class, 'documentable_id');
        }
        public function cajas() {
            return $this->belongsTo(Cajas::class, 'documentable_id');
        }
		public function solicitudes() {
            return $this->belongsTo(Solicitudes::class, 'documentable_id');
        }
        public function polizas() {
            return $this->belongsTo(Polizas::class, 'documentable_id');
        }
		public function fac_facturas() {
            return $this->belongsTo(FacturaSeguro::class, 'documentable_id');
        }

		public function scopeDeFiltro($query, $campo)
	    {           
			$queryFilter = new \Flexio\Modulo\Documentos\Services\DocumentoFilters;
	        return $queryFilter->apply($query, array_filter($campo));
	    }

        public function present()
        {
            return new \Flexio\Modulo\Documentos\Presenter\DocumentoPresenter($this);
        }

        public function tipo()
        {
            return $this->belongsTo('Flexio\Modulo\Documentos\Models\TipoDocumentos', 'tipo_id');
        }

        public function centro_contable()
        {
            return $this->belongsTo('Flexio\Modulo\CentrosContables\Models\CentrosContables', 'centro_contable_id');
        }

        public function catalogo_etapa()
        {
            return $this->belongsTo('Flexio\Modulo\Catalogos\Models\Catalogo', 'etapa', 'etiqueta')->where('modulo', 'documentos');
        }
        public function historial(){
	  return $this->hasMany(DocumentosHistorial::class,'documento_id');
        }
        public function getEnlaceBitacoraAttribute() {
        return base_url('documentos/historial/'.$this->uuid_documento);
        }       
        public function documentos(){
        return $this->belongsTo(Documentos::class, 'padre_id');
        }
        public function documentos_item(){
        return $this->hasMany(Documentos::class,'padre_id','id');
        }
}
