Vue.directive('select2ajax', require('./../../vue/directives/select2ajax.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));
Vue.directive('select2', require('./../../vue/directives/select2.vue'));

Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));

var movimiento_monetario_div = new Vue({

	el: "#movimiento_monetario_div",

	data: function () {
		var context = this;
		return {

			comentario: {

				comentarios: [],
				comentable_type: window.modulo == 'recibo_dinero' ? "Flexio\\Modulo\\MovimientosMonetarios\\Models\\MovimientoRecibo"
				: "Flexio\\Modulo\\MovimientosMonetarios\\Models\\MovimientosRetiros",
				comentable_id: '',

			},

			config: {

				vista: window.vista,
				modulo: window.modulo,
				select2: {
					width: '100%'
				},
				select2empezableId: {
					ajax: {
						url: function (params) {
							return phost() + 'proveedores/ajax_get_proveedores';
						},
						data: function (params) {
							return {
								q: params.term
							};
						}
					}
				},
				datepicker2: {
					dateFormat: "dd/mm/yy",
					maxDate:moment().format('DD/MM/YYYY')
				},
				disableDetalle: false

			},

			catalogos: {},

			detalle: {
				id: '',
				codigo: '',
				nombre: '',
				cuenta_id:'',
				fecha_entrada: moment().format('DD/MM/YYYY'),
				usuario_id: '',
				incluir_narracion: false,
				transacciones: [{
					id: '',
					nombre: '',
					cuenta_id: '',
					centro_id: '',
					debito: 0,
					credito: 0
				}]
			},
			empezable: {
				label: window.modulo == 'retiro_dinero' ? 'Empezar el retiro de dinero desde' : 'Empezar el recibo de dinero desde',
				type: '',
				types: [
					//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
					{
						id: 'proveedor',
						nombre: 'Proveedor'
					}
				],
				id: '',
				proveedors: []
			},

		};
	},

	components: {
		'empezar_desde': require('./../../vue/components/empezar-desde.vue'),
		'detalle': require('./../entrada_manual/components/detalle.vue'),
		'transacciones': require('./../entrada_manual/components/transacciones.vue'),
		'vista_comments': require('./../../vue/components/comentario.vue')
	},

	computed: {
		//...
	},

	methods: {
		getCancelUrl:function(){
			var context = this;
			if(context.config.modulo == 'recibo_dinero'){
				return phost() + 'movimiento_monetario/listar_recibos';
			}
			return phost() + 'movimiento_monetario/listar_retiros';
		},
		round2: function (v) {
			return parseFloat(roundNumber(v, 2));
		},

		guardar: function () {
			var context = this;
			var $form = $("#movimiento_monetario_form");

			$form.validate({
				ignore: '',
				wrapper: '',
				errorPlacement: function (error, element) {
					var self = $(element);
					if (self.closest('div').hasClass('input-group') && !self.closest('table').hasClass('itemsTable')) {
						element.parent().parent().append(error);
					} else if (self.closest('div').hasClass('form-group') && !self.closest('table').hasClass('itemsTable')) {
						self.closest('div').append(error);
					} else if (self.closest('table').hasClass('itemsTable')) {
						$form.find('.tabla_dinamica_error').empty().append('<label class="error">Estos campos son obligatorios (*).</label>');
					} else {
						error.insertAfter(error);
					}
				},
				submitHandler: function (form) {
					$('input, select').prop('disabled', false);
					$('form').find(':submit').prop('disabled', true);
					form.submit();
				}
			});
		}

	},

	ready: function () {

		var context = this;

		if (context.config.vista == 'ver') {

			Vue.nextTick(function () {

				context.detalle = $.extend(context.detalle, JSON.parse(JSON.stringify(window.recibo_dinero)));
				context.$broadcast('eSetEmpezable', window.recibo_dinero.empezable);
				context.comentario.comentarios = JSON.parse(JSON.stringify(window.recibo_dinero.landing_comments));
				context.comentario.comentable_id = JSON.parse(JSON.stringify(window.recibo_dinero.id));

				context.config.disableDetalle = true;
			});

		} else {
            //created by
            context.detalle.usuario_id = window.usuario_id;
		}

	},
});

Vue.nextTick(function () {
	movimiento_monetario_div.guardar();
});
