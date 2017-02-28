Vue.directive('select2ajax', require('./../../vue/directives/select2ajax.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));

var manual_entry_div = new Vue({

	el: "#manual_entry_div",

	data: function () {
		var context = this;
		return {

			comentario: {

				comentarios: [],
				comentable_type: "Flexio\\Modulo\\EntradaManuales\\Models\\EntradaManual",
				comentable_id: '',

			},

			config: {

				vista: window.vista,
				datepicker2: {
					dateFormat: "dd/mm/yy"
				},
				disableDetalle: false

			},

			catalogos: {},

			detalle: {
				id: '',
				codigo: '',
				nombre: '',
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
			}

		};
	},

	components: {

		'detalle': require('./components/detalle.vue'),
		'transacciones': require('./components/transacciones.vue'),
		'vista_comments': require('./../../vue/components/comentario.vue')

	},

	computed: {


		diffDebitoCredito: function () {

			var context = this;
			var total_debito = _.sumBy(context.detalle.transacciones, function (trans) {
				return parseFloat(trans.debito) || 0;
			});

            var total_credito = _.sumBy(context.detalle.transacciones, function (trans) {
				return parseFloat(trans.credito) || 0;
			});
			if (context.round2(total_debito) != context.round2(total_credito)) {
				return true;
			}
			return false;

		}

	},

	methods: {
		round2: function (v) {
			return parseFloat(roundNumber(v, 2));
		},

		guardar: function () {
			var context = this;
			var $form = $("#manual_entry_form");

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
					$('#proveedor3').prop('disabled', false);
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

				context.detalle = JSON.parse(JSON.stringify(window.entrada_manual));
				context.comentario.comentarios = JSON.parse(JSON.stringify(window.entrada_manual.landing_comments));
				context.comentario.comentable_id = JSON.parse(JSON.stringify(window.entrada_manual.id));

				context.config.disableDetalle = true;
			});

		} else {
            //created by
            context.detalle.usuario_id = window.usuario_id;
		}

	},
});

Vue.nextTick(function () {
	manual_entry_div.guardar();
});
