<style></style>
<template src="./template/articulo.html"></template>

<script>
export default {
	props: {
		config: Object,
		campoDisabled: Boolean,
		listaArticulos: Array,
		catalogos: Object,
		parent_index: Number,
		row: Object,
	},
	data() {
		return {
			disabledArticulo: false, //se usa para inhabilitar miestras se espera respuesta del ajax
			fa_caret: 'fa-caret-right',
			item_url: 'inventarios/ajax_getnuevo_typehead_items?ventas=1',
			categoria_id: '',
			select2Config: {
				width: '100%'
			}
		};
	},
	components: {
		'typeahead': require('./../typeahead.vue')
	},
	directives: {
		'select2': require('./../../directives/select2.vue'),
		'item-comentario': require('./../../directives/item-comentario.vue'),
		'porcentaje': require('./../../directives/inputmask-porcentaje.vue'),
		'moneda': require('./../../directives/inputmask-currency.vue'),
		'cantidad': require('./../../directives/inputmask-decimal.vue')
	},
	ready() {
		this.select2Event();
	},
	destroyed() {
		this.select2OffEvent();
	},
	vuex: {
		getters: {
			listaPrecio: (state) => state.precio,
			currentEmpezableType: (state) => state.current,
			estado: (state) => state.estado,
		}
	},
	computed: {
		getListaPrecio() {
			return this.listaPrecio;
		},
		tienePrecio() {
			return _.isNull(this.getListaPrecio);
		},
		desabledPorEstado() {
			return !_.isNull(this.estado) && this.estado != "por_aprobar";
		},
		getCuentas() {
			return this.catalogos.cuentas;
		},
		disabledFila() {
			return this.tienePrecio && this.row.items.length === 0;
		},
		debeEditar() {
			return this.config.disableEditar && (this.currentEmpezableType !== null && this.currentEmpezableType != "contrato_venta");
		},
		getSubtotal() {
			let subtotal = parseFloat(this.row.cantidad) * parseFloat(this.row.precio_unidad);
			this.row.subtotal = subtotal;
			return subtotal;
		},
		permisoEditarPrecio() {
			let permisoEditarPrecio = typeof window.editar_precio !== 'undefined' ? window.editar_precio : 0;
			return this.campoDisabled && !permisoEditarPrecio;
		},
		subTotalDisabled() {
			return this.campoDisabled;
		},
		getDescuentoTotal() {
			let total_descuento = this.getSubtotal * (parseFloat(this.row.descuento) / 100);
			this.row.total_descuento = total_descuento;
			return total_descuento;
		},
		getImpuestoTotal() {
			let total_impuesto = this.getSubtotal * (parseFloat(this.row.impuesto) / 100);
			console.log(this.getSubtotal, this.row.impuesto);
			this.row.total_impuesto = total_impuesto;
			return total_impuesto;
		},
		getImpuestoActual() {
			let impuesto_id = this.row.impuesto_id;
			if (_.isEmpty(impuesto_id)) {
				return 0;
			}
			let impuesto = this.catalogos.impuestos.find((q) => q.id == impuesto_id);

			let cantidad_impuesto = _.isUndefined(impuesto) ? 0 : parseFloat(impuesto.impuesto);

			return cantidad_impuesto;
		}
	},
	methods: {
		select2Event() {

			$("#categoria" + this.parent_index).on("change", this.cambiarCategoria);
			$("#unidad" + this.parent_index).on("change", this.cambiarUnidad);
			$("#impuesto" + this.parent_index).on("change", this.cambiarImpuesto);
		},
		select2OffEvent() {
			$("#categoria" + this.parent_index).off("change");
			$("#unidad" + this.parent_index).off("change");
			$("#impuesto" + this.parent_index).off("change");
		},
		cambiarImpuesto(event) {
			let impuesto_id = event.target.value;

			if (_.isEmpty(impuesto_id)) {
				//mensaje /validacion
			}

			let cantidad_impuesto = this.getImpuestoActual;
			this.row.impuesto = cantidad_impuesto;
			this.row.total_impuesto = this.getSubtotal * (cantidad_impuesto / 100);
		},
		cambiarUnidad(event) {
			let unidad_id = this.row.unidad_id;

			if (_.isEmpty(unidad_id)) {
				//mensaje /validacion
				this.row.precio_unidad = 0;
				return '';
			}
			let unidad = this.row.unidades.find((q) => q.id == unidad_id);

			if (_.isUndefined(unidad)) {
				//mensaje
				this.row.precio_unidad = 0;
				return '';
			}

			this.row.precio_unidad = this.getPrecio() * parseFloat(unidad.factor_conversion);
		},
		cambiarCategoria(event) {
			event.preventDefault()
			event.stopPropagation();
			console.log(this.categoria_id, this.row.categoria_id);
			let categoria_id = event.target.value;
			this.categoria_id = event.target.value;
			if (_.isEmpty(categoria_id)) {
				this.limpiarFila();
				return;
			}
			//if(this.row.items.length === 0){
			let datos = {
				categoria_id: categoria_id,
				erptkn: tkn
			};
			let categorias = this.postAjax('ajax_catalogo/ajax_get_items', datos);
			categorias.then((response) => {
					this.logout(response);
					this.$nextTick(function () {
						this.row.items = response.data.items;
						this.$broadcast('fill-typeahead', response.data.items);
					});
				})
				.catch((response) => {
					console.log(response);
					toastr.error("la categoria con tiene demaciados items", 'Error');
					this.row.categoria_id = "";
				});
			// }
		},
<<<<<<< HEAD
        getPrecio(){
            if(this.tienePrecio){
                //mensaje de error
            }
            let precioActual = this.row.precios.find((q)=>q.id == this.getListaPrecio.id);
            if(_.isUndefined(precioActual)){
                toastr.warning("no existe para este item","precio");
                return 0;
            }
            return parseFloat(precioActual.precio) || 0;
        },
        getCuentaDelItem(cuenta){
            if(cuenta.length > 1 || cuenta.length ==0){
                return '';
            }
            return _.head(cuenta);
        }
    },
    events:{
        'update-item': function (item){
           //para el set de los catalogos items
           this.$nextTick(function(){
               this.row.items = [item]
               this.row.unidades = item.unidades;
               this.row.atributos = item.atributos;
               this.row.precios = item.precios;
               this.row.item_id = item.id;
           });

           this.$nextTick(function(){
               this.row.impuesto_id = item.impuesto_id;
               this.row.categoria_id = item.categoria[0].id;
               this.categoria_id = item.categoria[0].id;
               this.row.unidad_id = item.unidad_id;
               this.row.cuenta_id = this.getCuentaDelItem(item.cuenta_id);
               this.row.precio_unidad = this.getPrecio();
               this.row.impuesto = this.getImpuestoActual;
           });

        },
        'setNombre'(){
            this.$broadcast('set-typeahead-nombre',this.row.nombre);
        }
    },
    watch:{
        'row.item_id'(val, oldval){
            console.log(val, oldval);
        }
    }
 }
</script>
=======
		changeCaret() {
			this.fa_caret = this.fa_caret === 'fa-caret-right' ? 'fa-caret-down' : 'fa-caret-right';
		},
		removeRow(row, index) {

			if (this.listaArticulos.length > 1) {
				this.listaArticulos.splice(index, 1);
			} else {
				this.limpiarFila();
			}

		},
		limpiarFila() {
			this.row = {
				categoria_id: '',
				items: [],
				unidades: [],
				unidad_id: '',
				item_id: '',
				atributos: [],
				cantidad: 1,
				periodo_tarifario: '',
				tarifa: '',
				en_alquiler: 0,
				precio_unidad: "0.00",
				impuesto_id: '',
				descuento: '0.00',
				cuenta_id: '',
				atributo_text: '',
				atributo_id: '',
				comentario: '',
				facturado: true,
				cuentas: [],
				tipo_id: '',
				por_entregar: 0,
				entregado: 0,
				devuelto: 0,
				total_impuesto: 0,
				total_descuento: 0,
				impuesto: 0,
				subtotal: 0
			}
		},
		postAjax(ajaxUrl, datos) {
			return this.$http.post({
				url: window.phost() + ajaxUrl,
				method: 'POST',
				data: datos
			});
		},
		logout(response) {
			if (_.has(response.data, 'session')) {
				window.location.assign(window.phost());
				return;
			}
		},
		getPrecio() {
			if (this.tienePrecio) {
				//mensaje de error
			}
            if(_.isEmpty(this.getListaPrecio)){
                toastr.warning("Recuerde indicar la lista de precio", "Lista de precio");
				return 0;
            }
			let precioActual = this.row.precios.find((q) => q.id == this.getListaPrecio.id);
			if (_.isUndefined(precioActual)) {
				toastr.warning("no existe para este item", "precio");
				return 0;
			}
			return parseFloat(precioActual.precio) || 0;
		},
		getCuentaDelItem(cuenta) {
			if (cuenta.length > 1 || cuenta.length == 0) {
				return '';
			}
			return _.head(cuenta);
		}
	},
	events: {
		'update-item': function (item) {
			//para el set de los catalogos items
			this.$nextTick(function () {
				this.row.items = [item]
				this.row.unidades = item.unidades;
				this.row.atributos = item.atributos;
				this.row.precios = item.precios;
				this.row.item_id = item.id;
			});

			this.$nextTick(function () {
				this.row.impuesto_id = item.impuesto_id;
				this.row.categoria_id = item.categoria[0].id;
				this.categoria_id = item.categoria[0].id;
				this.row.unidad_id = item.unidad_id;
				this.row.cuenta_id = this.getCuentaDelItem(item.cuenta_id);
				this.row.precio_unidad = this.getPrecio();
				this.row.impuesto = this.getImpuestoActual;
			});

		},
		'setNombre' () {
			this.$broadcast('set-typeahead-nombre', this.row.nombre);
		}
	},
	watch: {
		'row.item_id' (val, oldval) {
			console.log(val, oldval);
		}
	}
}
</script>
>>>>>>> master
>>>>>>> 0ef8535b3227f3f488cc644c76e2591000f9a362
