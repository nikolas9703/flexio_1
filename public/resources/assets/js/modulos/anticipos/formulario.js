// jshint esversion:6
import {
	formulario
} from './data';
import guardar from './../../vue/mixins/metodo_guardar';
import {
	urls_catalogo,
	info_header
} from './empezable_url';
import {
	referencia
} from './referencia-url';
import {
	AnticiposLocalStorage
} from './clases/anticipo-local-storage';
import {
	AnticipoRelacion
} from './clases/anticipo-asignar-relacion';
import {
	ModuloReferenciaUrl
} from './clases/modulo-referencia';
import {
	formEmpezable
} from './../../vue/state/empezable';
import store from './vuex/store';

var formCrearAnticipos = new Vue({
	el: '#formCrearAnticipos',
	mixins: [guardar],
	data: {

		empezable: {
			urls_catalogo: urls_catalogo,
			datos_empezable: info_header
		},
		catalogoFormulario: {
			estados: [],
			anticipables: [],
			tipoable: [],
			depositable: [],
			metodo_anticipo: [],
			bancos: []
		},
		formulario: formulario,
		comentario: {
			comentarios: [],
			comentable_type: "Flexio\\Modulo\\Anticipos\\Models\\Anticipo",
			comentable_id: '',
		},
		header_empezable: formEmpezable,
		campoDisabled: {
			estadoDisabled: true,
			botonDisabled: false,
			anticipable: false,
			camposEditar: false
		},
		config: {
			vista: window.vista,
			disableEditar: false,
			acceso: window.acceso === 1 ? true : false,
			loading: true
		},
		referencia: referencia, //es cuando se hace referencia desde otro modulo
		monto_referencia: "0",
		estado_inicial: '',
		moduloPadre: AnticiposLocalStorage.moduloPadre,
		politica: [],
		pagos_no_anulados: [],
		pagos_anulados:[]
	},
	store: store,
	created() {
		this.cargarCatalogos();
	},
	components: {
		'empezar-desde': require('./../../vue/components/header-empezable.vue'),
		'vista_comments': require('./../../vue/components/comentario.vue')
	},
	directives: {
		'datepicker2': require('./../../vue/directives/datepicker.vue'),
		'fselect2': require('./../../vue/directives/select2.vue')
	},
	computed: {
		owner() {
			if (this.moduloPadre === 'compras') {
				return 'Proveedor';
			}
			return 'Cliente';
		},
		validacionMonto() {
			if (!_.isEmpty(this.header_empezable.empezable_type)) {
				return _.toNumber(this.formulario.monto) > _.toNumber(this.monto_referencia);
			}
			return false;
		},
		filtroEstados() {

			//cambiar esto a una clase hay demaciado condiciones
			var estados = [];
			switch (this.config.vista) {
			case 'crear':
				estados = this.catalogoFormulario.estados;
				break;
			case 'ver':
				if (this.estado_inicial === "aprobado" && this.moduloPadre ==='ventas') {
					estados = this.catalogoFormulario.estados.filter(est => est.etiqueta == 'aprobado');
				} else if (this.estado_inicial === "aprobado" && this.moduloPadre ==='compras') {
					if(this.puedeAparecerAnulado){
						estados = this.catalogoFormulario.estados.filter(est => est.etiqueta != 'por_aprobar');
					}else{
						estados = this.catalogoFormulario.estados.filter(est => est.etiqueta == 'aprobado');
					}
				}
				 else if (this.estado_inicial === "anulado") {
					estados = this.catalogoFormulario.estados.filter(est => est.etiqueta == 'anulado');
				} else if (this.estado_inicial === "por_aprobar") {
					estados = this.catalogoFormulario.estados;
				}
				break;
			}
			return estados;
		},
		filtroMetodoAnticipo() {
			if (this.moduloPadre === 'compras') {
				return this.catalogoFormulario.metodo_anticipo;
			}

			return this.catalogoFormulario.metodo_anticipo.filter(est => est.etiqueta != 'credito_favor');

		},
		Desabilitados() {
			return this.config.vista === "ver";
		},
		isAnuladoOrAprobado() {

			if(this.moduloPadre === 'compras' && this.config.vista =='ver'){
				if(this.puedeAparecerAnulado && this.estado_inicial === "aprobado"){
					return false;
				}
				return this.estado_inicial === "aprobado" || this.estado_inicial === "anulado";
			}else if (this.moduloPadre === 'ventas' && this.config.vista =='ver') {
				return this.estado_inicial === "aprobado" || this.estado_inicial === "anulado";
			}

			return false;
		},
		puedeAparecerAnulado(){
			return this.pagos_no_anulados.length === 0;
		}
	},
	methods: {
		cargarCatalogos() {
			var self = this;
			var datos = {
				erptkn: tkn
			};
			var catalogos = this.postAjax('anticipos/ajax_catalogo_formulario_anticipo', datos);
			catalogos.then((response) => {
				if (_.has(response.data, 'session')) {
					window.location.assign(window.phost());
					return;
				}
				self.$nextTick(function () {
					self.catalogoFormulario.anticipables = response.data.anticipables; // = proveedores o clientes
					self.catalogoFormulario.estados = response.data.estados;
					self.catalogoFormulario.tipoable = response.data.tipoable;
					self.catalogoFormulario.depositable = response.data.depositable;
					self.catalogoFormulario.metodo_anticipo = response.data.metodo_anticipo;
					self.catalogoFormulario.bancos = response.data.bancos;
					self.$store.dispatch('SET_EMPEZABLETYPE', self.empezable.datos_empezable.categoria);
					self.referenciaurl();
					self.getAnticipo();
					self.config.loading = false;
				});
				if (self.config.vista == 'crear') {
					self.campoDisabled.estadoDisabled = true;
				}
			});
		},
		postAjax(ajaxUrl, datos) {
			return this.$http.post({
				url: window.phost() + ajaxUrl,
				method: 'POST',
				data: datos
			});
		},
		aplicable(id, tipo = "local") { ///evento para select de proveedor o cliente
			if (_.toLength(id) > 0) {
				var filtrados = _.find(this.catalogoFormulario.anticipables, ['id', parseInt(id)]);

				if (!_.isUndefined(filtrados)) {

					this.formulario.anticipable_id = id;
					this.formulario.saldo_pendiente = filtrados.saldo_pendiente;
					this.formulario.credito = filtrados.credito;
					if (tipo == "empezable") this.campoDisabled.anticipable = true;
					if (tipo == "local") {
						Vue.nextTick(function () {
							$("#empezable_type").prop('disabled', true);
						});
					}

					if (this.config.vista == "crear") {
						this.formulario.opciones_metodo_acticipo.ach.nombre_banco_ach = filtrados.id_banco;
						this.formulario.opciones_metodo_acticipo.ach.cuenta = filtrados.numero_cuenta;
					}

				} else {
					this.cleanAplicable();
					this.campoDisabled.anticipable = true;
					this.campoDisabled.botonDisabled = true;
					toastr.error('No esta activo', this.owner);
				}
			}
		},
		findAplicable(empezable_id) {
			var filtrados = _.find(JSON.parse(JSON.stringify(this.header_empezable.catalogo)), (query) => {
				return query.id == empezable_id;
			});
			this.aplicable(filtrados.proveedor.id, "empezable");
		},
		cleanAplicable() {
			this.formulario.anticipable_id = "";
			this.formulario.saldo_pendiente = "0.00";
			this.campoDisabled.anticipable = false;
			this.formulario.opciones_metodo_acticipo.ach.nombre_banco_ach = "";
			this.formulario.opciones_metodo_acticipo.ach.cuenta = "";
		},
		referenciaurl() {

			var llaveReferencia = _.keys(this.referencia.desde);

			if (llaveReferencia.length > 0) {
				let keyModulo = _.head(llaveReferencia);
				var vieneDe = new ModuloReferenciaUrl(this, this.header_empezable);
				vieneDe[keyModulo]();
			}
		},
		getAnticipo() {

			if (!_.isUndefined(window.hex_anticipo) && this.config.vista == "ver") {
				var uuid = window.hex_anticipo;
				var self = this;
				var datos = {
					erptkn: tkn,
					uuid: uuid
				};
				var info = this.postAjax('anticipos/ajax_get_anticipo', datos);
				info.then(function (response) {

					this.formulario = _.merge(this.formulario, response.data);
					this.campoDisabled.estadoDisabled = false;
					this.campoDisabled.camposEditar = true;
					this.comentario.comentarios = response.data.landing_comments;
					this.comentario.comentable_id = response.data.id;
					this.estado_inicial = response.data.estado;
					this.politica = response.data.politica;

					var relacion = new AnticipoRelacion(this);
					if (this.moduloPadre === 'compras') {
						this.pagos_no_anulados = response.data.pagos_no_anulados;
						_.map(['orden_compra', 'subcontrato'], (val) => relacion[val](response.data));
					} else if (this.moduloPadre === 'ventas') {
						_.map(['orden_venta', 'contrato'], (val) => relacion[val](response.data));
					}

					this.$nextTick(function () {
						if (response.data.metodo_anticipo == "ach") {
							this.formulario.opciones_metodo_acticipo.ach.cuenta = response.data.referencia.cuenta;
							this.formulario.opciones_metodo_acticipo.ach.nombre_banco_ach = response.data.referencia.nombre_banco_ach;
						}

						if (response.data.metodo_anticipo == "cheque") {
							this.formulario.opciones_metodo_acticipo.cheque.numero_cheque = response.data.referencia.numero_cheque;
							this.formulario.opciones_metodo_acticipo.cheque.nombre_banco_cheque = response.data.referencia.nombre_banco_cheque;
						}
						$("#empezable_type").prop('disabled', true);
					});

				});
			}
		},
		aplicar_monto() {
			var catActual = this.filtroCatalogoEmpezable();
			if (!_.isUndefined(catActual)) {
				if (this.config.vista === 'crear') this.formulario.monto = catActual.monto;
				this.monto_referencia = catActual.monto;
			}
		},
		filtroCatalogoEmpezable() {
			var catalogo = this.header_empezable.catalogo;
			if (catalogo.length > 0) {
				var id = _.toInteger(this.header_empezable.empezable_id);
				return _.find(catalogo, function (query) {
					return query.id === id;
				});
			}
		},
		filtroPolitica() {
			var politica = this.politica;
			if (politica.length > 0) {
				var politica_unica = _.maxBy(politica, (o) => parseFloat(o.monto_limite));
				return politica_unica;
			}
			return [];
		},
		cambiarEstadoAnticipo(estadoACambiar) {

			if (estadoACambiar === 'aprobado' && !_.isEmpty(this.filtroPolitica()) && this.moduloPadre === 'compras') {
				var politica = this.filtroPolitica();
				if (this.formulario.monto > parseFloat(politica.monto_limite)) {
					this.campoDisabled.botonDisabled = true;
					toastr.info("El monto limite para su aprobaci\u00F3n es " + politica.monto_limite, "Mensaje");
				}
			} else if (estadoACambiar === 'aprobado' && _.isEmpty(this.filtroPolitica()) && this.moduloPadre === 'compras') {
				this.campoDisabled.botonDisabled = true;
				toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");
			}
		}
	},
	watch: {
		'header_empezable.empezable_id': function (newVal, oldVal) {
			if (!_.isEmpty(newVal)) {
				this.findAplicable(newVal);
				this.aplicar_monto();
			} else {
				this.cleanAplicable();
			}
		},
		'formulario.anticipable_id': function (newVal, oldVal) {
			this.aplicable(newVal);
		}
	}
});

// ejecuta el cambio de proveedor / cliente
/*formCrearAnticipos.$watch('formulario.anticipable_id', function (newVal, oldVal) {
  this.aplicable(newVal);
});*/
