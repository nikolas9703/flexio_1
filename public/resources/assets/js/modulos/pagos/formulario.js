Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.directive('select2ajax', require('./../../vue/directives/select2ajax.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));

var form_crear_pago = new Vue({

	el: "#form_crear_pago_div",

	data: function () {
		var context = this;
		return {

			comentario: {

				comentarios: [],
				comentable_type: "Flexio\\Modulo\\Pagos\\Models\\Pagos",
				comentable_id: '',

			},

			config: {

				vista: window.vista,
				select2: {
					width: '100%'
				},
				datepicker2: {
					dateFormat: "dd/mm/yy"
				},
				inputmask: {

					currency: {
						'mask': '9{1,8}[.9{0,2}]',
						'greedy': false
					},
					currency2: {
						alias: 'currency',
						prefix: "",
						autoUnmask: true,
						removeMaskOnSubmit: true
					}

				},
				disableEmpezarDesde: false,
				disableDetalle: false,
				select2empezableId: {
					ajax: {
						url: function (params) {
							return phost() + 'pagos/ajax-get-empezables?empezable_type=' + context.empezable.type;
						},
						data: function (params) {
							return {
								q: params.term
							};
						}
					}
				}

			},

			catalogos: {
				proveedores: [],
				cuentas: [],
				cajas: [],
				cuenta_contable: [],
				metodos_pago: window.metodos_pago, //short catalogs
				bancos: window.bancos, //short catalogs
				estados: window.estados, //short catalogs
				tipos_pago: window.tipos_pago, //short catalogs
				aux: {}
			},

			detalle: {
				id: '',
				codigo: '',
				fecha_pago: moment().format('DD/MM/YYYY'),
				proveedor_id: '',
				monto_pagado: '',
				estado: 'por_aprobar',
				formulario: '',
				depositable_type: '',
				depositable_id: '',
				saldo_proveedor: 0, //components/detalle
				credito_proveedor: 0, //components/detalle
				pagables: [],
				metodos_pago: [{
					tipo_pago: '',
					total_pagado: 0,
					referencia: {
						nombre_banco_ach: '',
						cuenta_proveedor: '',
						numero_cheque: '',
						nombre_banco_cheque: '',
						numero_tarjeta: '',
						numero_recibo: ''
					}
				}]

			},

			empezable: {
				label: 'Aplicar pago a',
				type: '',
				types: [
					//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
					{
						id: 'factura',
						nombre: 'Factura'
					}, {
						id: 'proveedor',
						nombre: 'Proveedor'
					}, {
						id: 'subcontrato',
						nombre: 'Subcontrato'
					}, {
						id: 'anticipo',
						nombre: 'Anticipo'
					}
				],
				id: '',
				facturas: [],
				proveedors: [],
				subcontratos: [],
				anticipos: []
			},

		};
	},

	components: {

		'empezar_desde': require('./../../vue/components/empezar-desde.vue'),
		'detalle': require('./components/detalle.vue'),
		'pagables': require('./components/pagables.vue'),
		'monto': require('./components/monto.vue'),
		'metodos_pago': require('./components/metodos_pago.vue'),
		'vista_comments': require('./../../vue/components/comentario.vue')

	},

	computed: {

        sePuedeGuardar: function () {

			var context = this;
			var monto = _.sumBy(context.detalle.pagables, function (pagable) {
				return parseFloat(pagable.monto_pagado) || 0;
			});

			var pagado = _.sumBy(context.detalle.metodos_pago, function (metodo_pago) {
				return parseFloat(metodo_pago.total_pagado) || 0;
			});

            if (context.round2(monto) != context.round2(pagado) || (pagado === 0)) {
				return false;
			}
			return true;

		}

	},

	methods: {

		round: function (v) {
			return parseFloat(roundNumber(v, 4));
		},

		round2: function (v) {
			return parseFloat(roundNumber(v, 2));
		},

		guardar: function () {
			var context = this;
			var $form = $("#form_crear_pago");

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

		if (context.config.vista == 'editar') {

			Vue.nextTick(function () {

				context.empezable = $.extend(context.empezable, JSON.parse(JSON.stringify(window.empezable)));
				context.detalle = JSON.parse(JSON.stringify(window.pago));
				context.comentario.comentarios = JSON.parse(JSON.stringify(window.pago.comentario_timeline));
				context.comentario.comentable_id = JSON.parse(JSON.stringify(window.pago.id));

				context.config.disableEmpezarDesde = true;
				context.config.disableDetalle = true;

				if (politica_transaccion.length === 0) {
					if(localStorage['ms-selected'] == "seguros") {
                    		//$(".fa.fa-shopping-cart").html();
                    		$(".row.border-bottom.white-bg").html('<div class="row border-bottom white-bg" '+
                    			'style="padding-top:6px; padding-bottom:6px;">'+
	'<div class="col-xs-0 col-sm-4 col-md-6 col-lg-6">'+
		'<h2 class="hidden-xs hidden-sm" style="margin:0;">'+$("h2.hidden-xs.hidden-sm").html()+'</h2>'+                
'<div class="col-xs-7 col-sm-8 col-md-6 col-lg-6">'+
              '<ol class="breadcrumb">'+
           '<li>Seguros</li><li>Pagos</li><li class="active"><b>Detalle</b></li>       </ol>'+              
   '</div>	</div> <div class="col-xs-12 col-sm-12 col-md-3 col-lg-4 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-2"> <div id="moduloOpciones" class="btn-group btn-group-sm pull-right" style="margin:6px 12px 6px 0;"> <button class="btn btn-primary" type="button" id="http://166.78.244.188/desarrollo2/flexio/#" data-toggle="dropdown" aria-expanded="true" disabled="disabled">Acción</button> <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false" disabled="disabled"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button> <ul class="dropdown-menu "> <li><a href="http://166.78.244.188/desarrollo2/flexio/pagos/historial/11E6F9125B177EC08DCFBC764E11D717">Ver bitácora</a></li>				</ul> </div> <!-- Filtro Grupal de Botones --> <div id="filtroGroupBtns" class="btn-group btn-group-sm hidden-xs pull-right" role="group" style="margin:6px 4px 6px 0;"> </div> <!-- /Filtro Grupal de Botones --> </div> </div>'); 
                    	}
	
					console.log("que tal todos blas");
					toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");
					setTimeout(function () {
						$('.estado').attr('disabled', true);
						$('.btn-primary').attr('disabled', true);
					}, 400);
					return true;
				}

			});

		} else {
			context.config.enableWatch = true;

			Vue.nextTick(function () {

				context.empezable.type = window.empezable.type;
				Vue.nextTick(function () {

					context.empezable.id = window.empezable.id;

				});

			});
		}

	},
});

Vue.nextTick(function () {
	form_crear_pago.guardar();
});
