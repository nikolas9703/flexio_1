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

class Documentos extends Model
{
	protected $table = 'doc_documentos';
	protected $fillable = ['modulo_id', 'empresa_id', 'documentable_id', 'documentable_type', 'archivo_ruta', 'archivo_nombre', 'extra_datos','subido_por','nombre_documento'];
	protected $guarded = ['id'];

	public function documentable() {
		return $this->morpTo();
	}

        public function getNombreDocumentoAttribute($value) {

            if(empty($value)){
                return $this->archivo_nombre;
            }
            return $value;

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

	public function subido_por() {
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
        public function items() {
            return $this->belongsTo(Items::class, 'documentable_id');
        }
        public function cajas() {
            return $this->belongsTo(Cajas::class, 'documentable_id');
        }
}
