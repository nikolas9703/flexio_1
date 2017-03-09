Vue.transition('listado', {
	enterClass: 'fadeIn',
	leaveClass: 'fadeOut'
});

import Articulos from '../../../js/items';

var formularioCrearContratoAlquiler = new Vue({

	el: '#appContratoAlquiler',

	data: {

		//custom
		mostrar: {
			icono_pop_over: false
		},

		config: {
			modulo: 'contratos_alquiler',
			select2: {
				width: '100%'
			},
			vista: window.vista,
			enableWatch: false,
			disableEmpezarDesde: (window.disableEmpezarDesde == 0) ? false : true,
			//case #1982-1 se habilita el campo de cliente
			disabledClienteId: false,
		},

		catalogos: {

			cuentas: window.cuentas,
			periodos_tarifario: _.orderBy(window.ciclos_tarifarios, ['orden'], ['asc']),
			impuestos: window.impuestos,
			categorias: window.categorias

		},

		detalle: {
			enableWatch: true
		},
		empezable: {
			label: 'Empezar contrato desde',
			type: '',
			/*types:[
			    {id:'cliente',nombre:'Cliente'},//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
			    {id:'cotizacion',nombre:'Cotizacion'}//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
			],*/
			types: window.empezable.types,
			id: '',
			clientes: window.clientes,
			cotizacions: [],
		},
		vista: vista,
		disabledHeader: false,
		disabledEstado: true,
		disabledEditar: false,
		disabledVendedor: false,
		corte_dia_req: 'false',
		clientes: clientes, //catalogos from controller
		vendedores: vendedores, //catalogos from controller
		estados: estados, //catalogos from controller
		cortes_facturacion: cortes_facturacion, //catalogos from controller
		costos_retorno: costos_retorno, //catalogos from controller
		centros_contables: centros_contables, //catalogos from controller
		dia_corte: dia_corte, //catalogos from controller
		preguntas_cerrada: preguntas_cerrada, //catalogos from controller
		lista_precio_alquiler: lista_precio_alquiler,
		dispStyle: 'none',
		showFacturacion: 'inherit',
		contrato_alquiler: {
			id: '',
			codigo: codigo,
			cliente_id: '',
			centros_facturacion: [],
			centro_facturacion_id: '',
			corte_facturacion_id: '',
			calculo_costo_retorno_id: '',
			facturar_contra_entrega_id: pregunta_cerrada_default,
			lista_precio_alquiler_id: '',
			saldo: '',
			credito: '',
			vendedor_id: '',
			centro_contable_id: '',
			dia_corte: '',
			estado_id: '1', //por aprobar
			observaciones: '',
			articulos: Articulos.items
		},

	},

	components: {

		'articulo-agrupador': require('./../../vue/components/articulo-agrupador.vue')

	},

	ready: function () {
                console.log('ready_formulario');
		var context = this;
		if (context.vista == 'editar') {

			Vue.nextTick(function () {
				//context.empezable = $.extend({label:context.empezable.label,types:context.empezable.types},window.empezable);
				context.contrato_alquiler = contrato_alquiler;
				//mutable
				Articulos.items = context.contrato_alquiler.articulos;

				context.disabledEstado = false;

				if (context.contrato_alquiler.estado_id > '2') //anulado o terminado
				{
					context.disabledEditar = true;
				}
			});
		}

		this.updateCampos();

		Vue.nextTick(function () {

			context.config.enableWatch = true;

			if (context.vista == 'crear') {

				context.empezable.type = window.empezable.type;
				Vue.nextTick(function () {
					context.empezable.id = window.empezable.id;
				});

			}
		});

		if (window.acceso == '0') {
			context.disabledEditar = true;
		}

	},

	computed: {

        getItemsDuplicados: function(){

            var context = this;
            var i = 0;
            var duplicados = 0;
            _.forEach(context.contrato_alquiler.articulos, function(articulo){
                var j = 0;
                var aux = _.filter(context.contrato_alquiler.articulos, function (o){
                    j++;



                    return (i != j - 1) && (
                        context.comparar(articulo.categoria_id, o.categoria_id) &&
                        context.comparar(articulo.item_id, o.item_id) &&
                        (
                            (articulo.atributos.length && articulo.atributo_id !== '' && articulo.atributo_id == o.atributo_id) ||
                            context.comparar(articulo.atributo_text, o.atributo_text)
                        ) &&
                        context.comparar(articulo.periodo_tarifario, o.periodo_tarifario) &&
                        (articulo.precio_unidad != '0.00' && articulo.precio_unidad == o.precio_unidad)
                    );
                });
                duplicados += aux.length ? 1 : 0;
                i++;
            });
            if(duplicados > 0){toastr['error']('Items duplicados');}
            return duplicados > 0 ? true : false;

        },

		disabledCorteFacturacion: function () {
			//this.$set('contrato_alquiler.dia_corte','');

			if (_.includes([11, 20], this.contrato_alquiler.corte_facturacion_id)) {
				//make required
				this.corte_dia_req = 'true';
				this.dispStyle = 'inherit';
				return false;

			} else {
				this.dispStyle = 'none';
				this.corte_dia_req = 'false';
				return true;

			}

		},
		getEstados: function () {
			//check entregas estados y disable anular option
			if (typeof disableAnular != 'undefined') {
				if (disableAnular == 0) {
					//disable anular option
					return _.filter(this.estados, function (estado) {
						return estado.id != '3';
					});
				} else {
					return this.estados;
				}
			} else {
				return this.estados;
			}

		}

	},
	methods: {

        comparar: function(a, b){
            return (a !== '' && a == b);
        },

		updateCampos: function () {
			if (typeof centros_contables != 'undefined') {
				if (centros_contables.length == 1) {
					this.contrato_alquiler.centro_contable_id = centros_contables[0].id;
				}

				//case #1682.1 si el usuario es vendedor deshabilitar campo y setear el usuario
				if (vendedor == 0) {
					this.disabledVendedor = true;
					if(this.empezable.type === '')
					{
						this.contrato_alquiler.vendedor_id = userId;
					}
				}
			}

		},
		cambioDeCorteFacturacion: function (tipo) {
			this.$set('contrato_alquiler.dia_corte', '');
		},
		guardar: function () {
			var context = this;
			var $form = $("#form_crear_contrato_alquiler");
			var tableErrors = $("#contratosAlquilerItemsErros");

			$form.validate({
				ignore: '',
				wrapper: '',
				errorPlacement: function (error, element) {
					var self = $(element);
					tableErrors.html(' ');
					if (self.closest('table').length > 0) {
						tableErrors.html('<label class"error" style="color:red;">Estos campos son obligatorios</label>');
					} else {
						error.insertAfter(element);
					}
				},
				submitHandler: function (form) {
					context.disabledHeader = false;
					context.disabledEstado = false;
					$('input, select').prop('disabled', false);
					Vue.nextTick(function () {
						form.submit();
					});
				}
			});
		},
		filtrarCatalogoFacturacionRecurrente: function () {
			var scope = this;
			var selected = _.find(this.preguntas_cerrada, function (query) {
				return query.id == scope.contrato_alquiler.facturar_contra_entrega_id;
			});

			if (selected.nombre.match(/si/gi)) {
				var cortes = _.filter(scope.cortes_facturacion, function (o) {
					return !o.nombre.match(/sin/gi);
				});
				//Todos los estados
				Vue.nextTick(function () {
					scope.cortes_facturacion = cortes;
				});
				return false;
			}

			//Todos los estados
			Vue.nextTick(function () {
				scope.cortes_facturacion = cortes_facturacion;
			});
			return false;
		}
	},
	watch: { //5,2
		'empezable.id': function (val, oldVal) {
			var context = this;

			if (context.empezable.type == 'cotizacion' && val != '') {
				var comenzable_id = context.empezable.id;
				var cotizacion = _.find(context.empezable.cotizacions, function (cotizacion) {
					return cotizacion.id == comenzable_id;
				});

				Vue.nextTick(function () {
					context.contrato_alquiler.observaciones = cotizacion.observaciones;
					context.contrato_alquiler.cliente_id = cotizacion.cliente_id;
					context.contrato_alquiler.vendedor_id = cotizacion.vendedor_id;
				});
				context.$broadcast('popular_articulos', cotizacion.articulos); //contratos_items*/
			}

			if (this.empezable.id != '') {
				this.config.disabledClienteId = true;
			} else {
				this.config.disabledClienteId = false;
				context.contrato_alquiler.vendedor_id = userId;
			}

		},
		'empezable.type': function (val, oldVal) {
			var context = this;
			if (oldVal != '')
				context.$broadcast('limpiando_articulos');
			if (this.empezable.type != '') {
				this.config.disabledClienteId = true;
			} else {
				this.config.disabledClienteId = false;
			}
			if (this.empezable.type == 'cotizacion') {
				this.empezable.cotizacions = cotizacions;
			}
		},
		'contrato_alquiler.cliente_id': function (val, oldVal) {

            var context = this;
			this.updateCampos();
			//case #1682.1 setear el default de lista de precio de alquiler
			if (val != '') {

				var id = 0;
				if (this.lista_precio_alquiler.length == 1) {
					id = this.lista_precio_alquiler[0].id;
				} else {
					_.forEach(this.lista_precio_alquiler, function (precio) {
						if (precio.principal == 1) {
							id = precio.id;
						}
					});
				}
				this.contrato_alquiler.lista_precio_alquiler_id = id;
				//set default centro de facturacion
				var selectedClient = _.find(this.clientes, function (o) {
					return o.cliente_id == val;
				});

				this.contrato_alquiler.saldo = typeof selectedClient != 'undefined' && typeof selectedClient.saldo != 'undefined' ? selectedClient.saldo : "";
				this.contrato_alquiler.credito = typeof selectedClient != 'undefined' && typeof selectedClient.credito != 'undefined' ? selectedClient.credito : "";
				this.contrato_alquiler.centros_facturacion = typeof selectedClient != 'undefined' && typeof selectedClient.centros_facturacion != 'undefined' ? selectedClient.centros_facturacion : '';

				if (typeof selectedClient != 'undefined' && this.contrato_alquiler.centros_facturacion.length == 1) {
					this.contrato_alquiler.centro_facturacion_id = selectedClient.centros_facturacion[0].id;
				} else {
					if(this.contrato_alquiler.centros_facturacion!=''){
						_.forEach(selectedClient.centros_facturacion, function (centrofactura) {
							if (centrofactura.principal == 1) {
								//selecciona el centro de facturacion principal
								context.contrato_alquiler.centro_facturacion_id = centrofactura.id;
							}
						});
					}

					if (typeof selectedClient != 'undefined' && this.contrato_alquiler.centro_facturacion_id == '') {
						//si no tiene ninguna como default selecciona la primera de la lista
						this.contrato_alquiler.centro_facturacion_id = selectedClient.centros_facturacion[0].id;
					}
				}
			} else if (val == '') {
				this.config.disabledClienteId = false;
			}
		},
		'contrato_alquiler.facturar_contra_entrega_id': function (val, oldVal) {

			this.filtrarCatalogoFacturacionRecurrente();

		},
		'contrato_alquiler.lista_precio_alquiler_id': function (val, oldVal) {

			if (_.isEmpty(val) && this.vista != 'editar') {
				//Si no selecciona ninguna lista de precio
				//limpiar valores de periodo y tarifa.
				_.forEach(this.contrato_alquiler.articulos, function (articulo) {
					Vue.nextTick(function () {
						articulo.periodo_tarifario = '';
						articulo.tarifa = '';
					});
				});
				return;
			}

			//Al cambiar precio de lista
			//actualizar las tarifas.
			this.$broadcast('setTarifa');
		},

	},

});

Vue.nextTick(function () {
	formularioCrearContratoAlquiler.guardar();
});
