// jshint esversion:6
import {urls_catalogo, datos_del_header, tablasAlquiler} from './data/data-empezable';
import { referencia } from './data/desde-modulo';
import store from './vuex/store';
import { formEmpezable } from './../../vue/state/empezable';
import articulos from './data/data-articulos';

var form_crear_facturas1 = new Vue({

    el: "#formulario_factura_venta",

    data:{
        catalogoFormulario: {
			estados: [],
			clientes: [],
			vendedor: [],
			lista_precio: [],
			lista_precio_alquiler: [],
			termino_pago: [],
			centros_contables: [],
			catalogoItems:{impuestos:[], cuentas:[], categorias:[]}
		},
        empezable: {
			urls_catalogo: urls_catalogo,
			datos_empezable: datos_del_header
		},
        comentario: {
            comentarios: [],
            comentable_type: "Flexio\\Modulo\\FacturasVentas\\Models\\FacturaVenta",
            comentable_id: '',
        },
        config: {
            vista: window.vista,
            disableEditar: false,
			acceso: window.acceso === 1 ? true : false,
			loading: true
        },
		campoDisabled:{
			estadoDisabled:true
		},
        estado_inicial: '',
		referencia: referencia, //es cuando se hace referencia desde otro modulo e utilizar el empezable
		factura: {},
		formEmpezable: formEmpezable,
        tablaActual:'tabla-articulos',
        tablaAlquilerActual: '',
        articulos_alquiler: [],
        articulos: articulos.items
    },
    created() {
		this.cargarCatalogos();
		this.cargarValoresPorDefecto();
	},
    store: store,
	vuex: {
		getters: {
			empezable_type: (state) => state.empezable_type,
			currentEmpezableType: (state) => state.current,
			empezable_id: (state) => state.empezable_id,
			precio_venta_id: (state) => state.precio_venta_id
		}
	},
    components:{
        'empezar-desde': require('./../../vue/components/header-empezable.vue'),
        'formulario': require('./formulario/formulario.vue'),
        ///totales de la tabla
        'tabla-articulos': require('./../../vue/components/factura_venta/tabla-articulos.vue'),
        'tabla-articulos-alquiler':require('./../../vue/components/factura_venta/tabla-articulos-alquiler.vue'),
        'tabla-cargos-alquiler':require('./../../vue/components/factura_venta/tabla-cargos-alquiler.vue'),
        'totales':require('./../../vue/components/factura_venta/totales.vue'),
        'formulario-botones': require('./formulario/formulario-botones.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')
    },

    ready:function(){},
    methods:{
        cargarCatalogos() {
			var self = this;
			var datos = {
				erptkn: tkn,
				factura_id: window.hex_factura
			};
			var catalogos = this.postAjax('facturas_seguros/ajax_formulario_catalogos', datos);

			catalogos.then((response) => {
				this.logout(response);
				self.$nextTick(function () {
					self.catalogoFormulario.estados = response.data.estados;
					self.catalogoFormulario.clientes = response.data.clientes;
					self.catalogoFormulario.centros_contables = response.data.centros_contables;
					self.catalogoFormulario.categorias = response.data.categorias;
					self.catalogoFormulario.impuestos = response.data.impuestos;
					self.catalogoFormulario.lista_precio = response.data.lista_precio;
					self.catalogoFormulario.lista_precio_alquiler = response.data.lista_precio_alquiler;
					self.catalogoFormulario.termino_pago = response.data.termino_pago;
					self.catalogoFormulario.vendedor = response.data.vendedor;
					self.catalogoFormulario.catalogoItems.impuestos = response.data.impuestos;
					self.catalogoFormulario.catalogoItems.cuentas = response.data.cuentas;
					self.catalogoFormulario.catalogoItems.categorias = response.data.categorias;
					self.referenciaurl();
					self.getFactura();
					self.$store.dispatch('SET_EMPEZABLETYPE', self.empezable.datos_empezable.categoria);
          self.$store.dispatch('SET_ALQUILER_LISTA_PRECIO_VENTA_CAT', response.data.lista_precio);
					self.config.loading = false;
				});
			});
		},
    cargarValoresPorDefecto() {

      if(this.config.vista=='editar'){
        return false;
      }

      var self = this;
			var datos = { erptkn: tkn };
			/*var response = this.postAjax('facturas_seguros/ajax_default_values', datos);
      response.then((response) => {
				this.logout(response);
				self.$nextTick(function () {
					//self.catalogoFormulario.estados = response.data.estados;
          //self.$store.dispatch('SET_PRECIO_VENTA_ID', response.data.precios_venta_id);
				});
			});*/
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
		getFactura() {
			if (!_.isUndefined(window.hex_factura) && this.config.vista == "editar") {
				var uuid = window.hex_factura;
				var self = this;
				var datos = {
					erptkn: tkn,
					uuid: uuid
				};
				var info = this.postAjax('facturas_seguros/ajax_factura_info', datos);
				info.then(function (response) {
					self.logout(response);
                    if(!_.isNull(response.data.empezable_type)){
                        self.$store.dispatch('SET_CURRENT', response.data.empezable_type);
                    }
                    self.factura = response.data;
					self.comentario.comentarios = response.data.landing_comments;
					self.comentario.comentable_id = response.data.id;
					self.config.disableEditar = true;
				});
			}

		},
		referenciaurl() {
			var llaveReferencia = _.keys(this.referencia.desde);
			if (llaveReferencia.length > 0) {
				let keyModulo = _.head(llaveReferencia);
				this.$store.dispatch('SET_CURRENT', keyModulo);
				this.formEmpezable.empezable_type = keyModulo;
				this.formEmpezable.aux_empezable_id = this.referencia.desde[keyModulo];
				this.formEmpezable.empezable_id = this.referencia.desde[keyModulo];
			}
		}
	},
	events:{
		'OnCampoDisabled'(value){
			this.campoDisabled.estadoDisabled = value;
		},
		'OnArticulos'(items){
			//this.articulos = Object.assign({}, this.articulos, items);
			this.articulos = items;
			this.$nextTick(function(){
				this.$broadcast('setNombre');
			});

		},
		'OnArticulosAlquiler'(items){
			var scope = this;
      this.$nextTick(function(){
				scope.articulos_alquiler = items;
			});
		}
	},
	watch:{
		'currentEmpezableType'(newval,oldval){
			if(newval.match(/orden_alquiler|contrato_alquiler/gi)){
				this.tablaActual ='tabla-articulos-alquiler';
				this.tablaAlquilerActual = tablasAlquiler[newval];
			}else{
				this.tablaActual ='tabla-articulos';
			}
		}
	}
});
