<style>
.moneda {
	text-align: right
};
</style>
<template src="./formulario.html"> </template>

<script>
import guardar from './../../../vue/mixins/metodo_guardar';
import {
	formEmpezable
} from './../../../vue/state/empezable';
import {
	moduloCobrosInfo
} from './../clase/cobros-popular-formulario';
import {
	get_cobro
} from './../data/data-formulario';
export default {
	props: ['catalogos', 'cobro', 'config'],
	mixins: [guardar],
	data() {
		return {
			formEmpezable: formEmpezable,
			formulario: {
				id: '',
				saldo_pendiente: '0.00',
				credito: '0.00',
				fecha_pago: moment().format('DD/MM/YYYY'),
				monto: '0.00',
				depositable_id: '',
				depositable_type: 'banco',
				cliente_id: '',
				estado: 'aplicado'
			},
			clientes: [],
			facturas: [],
			filas_metodo_cobro: [{
				icon: 'fa fa-plus',
				tipo_pago: '',
				total_pagado: 0.00,
				referencia: {
					nombre_banco_ach: '',
					cuenta_cliente: '',
					numero_cheque: '',
					nombre_banco_cheque: ''
				}
			}],
			itemPago: [], //usado pra  la filas de las facturas
			montos: [],
			campoDisabled: {
				botonDisabled: false,
				camposEditar: false,
				estadoDisabled: true,
			},
			estado_inicial: '',
			mensajeErrorTotales: '',
			mensajeErrorCredito: '',
			filtar_metodo: false,
			cobrosUrl: window.phost() + 'cobros/listar',
			filtrar_facturas: false,
			cache_credito: 0,
			anticipo_cliente: []
		};
	},
	vuex: {
		getters: {
			empezable_type: (state) => state.empezable_type,
			currentEmpezableType: (state) => state.current,
			empezable_id: (state) => state.empezable_id
		}
	},
	filters: {
		moneda: require('./../../../vue/filters/currency-two-way.vue'),
	},
	computed: {
		filtroDepositable() {
			if (this.formulario.depositable_type == 'banco') {
				return this.catalogos.cuenta_bancos;
			}
			return this.catalogos.cajas;
		},
		filtroFacturas() {
			let facturas = this.facturas;

			if (this.filtrar_facturas) { //filtar las facturas cuando se cobra por credito y es cliente
				this.itemPago = [];
				this.montos = [];

				return facturas.filter((fac) => fac.tipo === 'normal');
			}
			return facturas;
		},
		filtroTipoDesposito() {
			let tipoDeposito = this.catalogos.depositable;
			if (this.filtrar_facturas && this.formulario.depositable_type === 'caja') {
				this.formulario.depositable_id = '';
				return tipoDeposito.filter((tipo) => tipo.etiqueta === 'banco');
			}
			return tipoDeposito;
		},
		monto() {
			if (this.montos.length === 0) {
				return 0;
			}
			return _.sum(this.montos);
		},
		total_cobrado() {
			if (this.montos.length === 0) {
				return 0;
			}
			return total_cobrado = _.sumBy(this.filas_metodo_cobro, (o) => parseFloat(o.total_pagado));
		},
		validacionMonto() {
			if (this.monto !== this.total_cobrado && !this.isEditar) {
				this.mensajeErrorTotales = "El total debe ser igual al monto";
				return true;
			}
			this.mensajeErrorTotales = "";
			return false;
		},
		isEditar() {
			return this.config.vista == 'ver';
		},
		filtroEstado() {
			if (this.config.vista == 'crear') {
				return [];
			}
			if (this.estado_inicial === "aplicado") {
				return this.catalogos.estados;
			}

			if (this.estado_inicial === "anulado") {
				return this.catalogos.estados.filter(est => est.etiqueta == 'anulado');
			}

		},
		filtroMetodoCobros() {
			if (this.filtar_metodo) {
				return this.catalogos.metodo_cobro.filter(mtd => mtd.etiqueta != 'credito_favor');
			}
			return this.catalogos.metodo_cobro;
		},
		formatoDinamico() {
			if (this.facturas.length > 0 || this.filas_metodo_cobro.length > 0) {
				this.$nextTick(function () {
					this.inputmask_currency();
				});
			}
		},
		isAnulado() {
			return this.estado_inicial === "anulado";
		}
	},
	methods: {
		llenarFormulario(selecionado) {
			let formulario = new moduloCobrosInfo(this, selecionado);
			formulario[this.currentEmpezableType]();
		},
		setDatosCobros(datos) {
			let formulario = new moduloCobrosInfo(this, datos);
			formulario.editar();
		},
		pagado(cobros) {
			return _.sumBy(cobros, (o) => parseFloat(o.pivot.monto_pagado)) || 0;
		},
		saldo_pendiente(factura) {
			let saldo_pendiente = parseFloat(factura.total) - this.pagado(factura.cobros);
			return parseFloat(accounting.toFixed(saldo_pendiente, 2));
		},
		getSaldoCobrar(factura, i) {
			this.inputmask_currency();
			if (_.isUndefined(this.itemPago[i])) {
				this.itemPago.$set(i, this.saldo_pendiente(factura));
				this.montos.$set(i, this.saldo_pendiente(factura));
			}
		},
		cambiarCantidad(i, event, factura) {
			var total = parseFloat(accounting.unformat(event.target.value)) || 0;

			if (total > this.saldo_pendiente(factura)) {
				toastr.error("El pago no puede mayor a la cantidad a cobrar", 'Cobros');
				this.campoDisabled.botonDisabled = true;
				return;
			}
			this.itemPago.$set(i, total);
			this.montos.$set(i, total);
			this.campoDisabled.botonDisabled = false;
		},
		addRow() {
			this.filas_metodo_cobro.push({
				icon: 'fa fa-trash',
				tipo_pago: '',
				total_pagado: 0.00,
				referencia: {
					nombre_banco_ach: '',
					cuenta_cliente: '',
					numero_cheque: '',
					nombre_banco_cheque: ''
				}
			});
			this.formatoDinamico;
		},
		deleteRow(row) {
			this.filas_metodo_cobro.$remove(row);
			this.formatoDinamico;
		},
		logica_credito() {
			var metodo_credito = this.filas_metodo_cobro.filter((met) => met.tipo_pago == 'credito_favor');
			var monto_pagado = _.sumBy(metodo_credito, (o) => parseFloat(o.total_pagado));
			var credito = parseFloat(this.formulario.credito);
			if (monto_pagado > credito) {
				this.mensajeErrorCredito = "su credito es insuficiente para realizar el cobro";
				this.campoDisabled.botonDisabled = true;
				return;
			} else {
				this.mensajeErrorCredito = "";
				this.campoDisabled.botonDisabled = false;
				return;
			}
		},
		anticipos_credito(row, ev) {
			if (row.tipo_pago == 'credito_favor') {
				var facturas = this.facturas;
				if (this.currentEmpezableType === 'cliente') {
					this.filtrar_facturas = true;
					//actualizar el credito
					if (ev.type === "change") this.calculo_credito();
				}
				return this.logica_credito();
			} else {
				if (this.cache_credito > 0) {
					this.formulario.credito = this.cache_credito;
				}
			}
			this.filtrar_facturas = false;
			this.mensajeErrorCredito = "";
			this.campoDisabled.botonDisabled = false;
			return;
		},
		inputmask_currency() {
			this.$nextTick(function () {
				$(".moneda").inputmask('currency', {
					prefix: "",
					autoUnmask: true,
					removeMaskOnSubmit: true
				});
			});
		},
		calculo_credito() {
			var anticipos_facturas = this.facturas.filter((met) => met.tipo == 'anticipo');
			var total_anticipo = _.sumBy(anticipos_facturas, (o) => o.total_anticipo);
			this.formulario.credito = Math.abs(parseFloat(this.formulario.credito) - total_anticipo);
		},
		limpiarCampos() {
			this.facturas = [];
			this.formulario.cliente_id = "";
			this.formulario.credito = 0;
			this.formulario.saldo_pendiente = 0;
			this.limpiar_otros_datos();
		},
		limpiar_otros_datos() {
			this.filas_metodo_cobro.forEach(function (fila) {
				fila.tipo_pago = '';
			});
			this.filtar_metodo = false;
			this.filtrar_facturas = false;
			this.cache_credito = 0;
		}
	},
	directives: {
		'datepicker2': require('./../../../vue/directives/datepicker.vue')
	},
	events: {
        setCobroByFactura: function(cobro){
            var context = this;
            context.itemPago = [];
            _.forEach(cobro.factura_cobros, function(factura_cobros){
                context.itemPago.push(factura_cobros.pivot.monto_pagado);
            });
        }
	},
	watch: {
		'empezable_id' (val, oldVal) {
			if (!_.isEmpty(val) && this.config.vista == "crear") {
				this.limpiar_otros_datos();
				this.formEmpezable.opcionSeleccionada = _.find(this.formEmpezable.catalogo, (cat => cat.id == val));
				this.llenarFormulario(this.formEmpezable.opcionSeleccionada);
			}
		},
		'cobro' (val, oldVal) {
			this.setDatosCobros(val);
		},
		'currentEmpezableType' (val, oldVal) {
			if (this.config.vista == 'crear') {
				var self = this;
				var empezable_id = this.formEmpezable.aux_empezable_id;
				Vue.nextTick(function () {
					self.formEmpezable.empezable_type = val;
					self.formEmpezable.aux_empezable_id = empezable_id;
					self.formEmpezable.empezable_id = empezable_id;
					self.limpiarCampos();
				});
			}
		},
		'filas_metodo_cobro' (val, oldval) {
			var metodo_credito = this.filas_metodo_cobro.filter((met) => met.tipo_pago == 'credito_favor');
			if (metodo_credito.length > 0) {
				this.logica_credito();
			} else {
				this.mensajeErrorCredito = "";
				this.campoDisabled.botonDisabled = false;
			}
		}
	}
}
</script>
