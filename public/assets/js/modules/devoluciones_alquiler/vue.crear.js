//Directiva para campos Chosen
Vue.directive('chosen', {
	twoWay: true,
	bind: function () {
		var scope = this;
		var formulario = $(scope.$el);

		setTimeout(function () {
			$(scope.el).chosen({
				width: '100%',
				inherit_select_classes: true
			}).on('chosen:ready', function (e, params) {
				//Ejecutar trigger change
				$(scope.el).trigger('chosen:updated');
			}).trigger('chosen:ready').change(function (e) {
				scope.set(scope.el.value);
			})
		}.bind(this), 500);
	},
	update: function (nv, ov) {
		var scope = this;
		var formulario = $(scope.$el);

		// note that we have to notify chosen about update
		setTimeout(function () {
			$(scope.el).trigger("chosen:updated");
		}, 500);
	}
});

Vue.directive('select2', {
    twoWay: true,
    params: ['config'],
    bind: function () {
        var self = this;
        setTimeout(function () {
          $(self.el)
          .select2(self.params.config)
          .on('change', function () {
                    var ele = this;
                    setTimeout(function(){
                        if(self.el === null)return;
                        self.set($(ele).val());
                    });
                });
        }, 500);
    },
    update: function (value) {
			setTimeout(function () {
				$(this.el).val(value).trigger('change');
			}, 500);
    },
    unbind: function () {
			setTimeout(function () {
        $(this.el).off().select2('destroy');
			}, 500);
    }
});

Vue.directive('datepicker', {
	bind: function () {
		var vm = this.vm;
		var key = this.expression;
		var context = this;
		$(this.el).datepicker({
			dateFormat: "dd/mm/yy",
			onSelect: function (date) {
				vm.$set(key, date);
			},
			onClose: function (selectedDate) {}
		});
	},
	update: function (val) {
		$(this.el).datepicker('setDate', val);
	}
});

Vue.http.options.emulateJSON = true;
var ODTModel = new Vue({
	el: '#form_devoluciones_alquiler',
	ready: function () {
		var context = this;
		if (context.vista == 'editar') {
			var devolucion_alquiler_json = JSON.parse(devolucion_alquiler);

			Vue.nextTick(function () {
				context.devolucion_alquiler = devolucion_alquiler_json;
				console.log(context.devolucion_alquiler);
			});

			context.disabledEstado = false;
			context.disabledHeader = true;
			context.disabledHeaderEmpezableId = true;

			if (devolucion_alquiler_json.estado_id == 2 || devolucion_alquiler_json.estado_id == 3) {
				context.disabledEditar = true;
			}

		}
		if(window.acceso == '0'){context.disabledEditar = true;}

		//form validate
		$("#form_devoluciones_alquiler").validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
			errorPlacement: function (error, element) {
				var tableErrors = $("#devolucionesAlquilerItemsError");
				var self = $(element);
				tableErrors.html(' ');
				if (self.closest('table').length > 0) {
					tableErrors.html('<label class"error" style="color:red;">Estos campos son obligatorios</label>');
				} else {
					error.insertAfter(element);
				}
			},
		});
	},

	data: {
		vista: vista,
		disabledHeader: false,
		disabledHeaderEmpezableId: true,
		disabledEstadoId: true,
		disabledEditar: false,
		empezables: typeof window.empezables != 'undefined' ? window.empezables : [],
		clienteOptions: typeof window.clientesArray != 'undefined' ? window.clientesArray : [],
		vendedoresOptions: typeof window.vendedoresArray != 'undefined' ? vendedoresArray : [],
		recibidosOptions: typeof window.recibidosArray != 'undefined' ? window.recibidosArray : [],
		estadosOptions: typeof window.estadosArray != 'undefined' ? $.parseJSON(estadosArray) : [],
		saldo_pendiente_acumulado: '',
		credito_favor: '',
		//fecha_alquiler: typeof fecha_alquiler != 'undefined' && _.isString(fecha_alquiler) ? fecha_alquiler : '',
		fecha_inicio_contrato: '',
		fecha_fin_contrato: '',
		fecha_devolucion: typeof fecha_devolucion != 'undefined' && _.isString(fecha_devolucion) ? fecha_devolucion : '',
		modal: {
			titulo: '',
			contenido: '',
			footer: ''
		},
		recibido_id: usuario_id,
		cliente_id: typeof cliente_id != 'undefined' && _.isNumber(cliente_id) ? cliente_id : '',
		vendedor_id: typeof vendedor_id != 'undefined' && _.isNumber(vendedor_id) ? vendedor_id : '',
		estado_id: typeof estado_id != 'undefined' && _.isNumber(estado_id) ? estado_id : '',

		devolucion_alquiler: {
			id: '',
			empezar_desde_type: '',
			empezar_desde_id: '',
			cliente_id: '',
			vendedor_id: '',
			estado_id: '1' //por aprobar
		},
		guardarBtn: 'Guardar',
		guardarBtnDisabled: true
	},

	methods: {
		ajax: function (url, data) {
			var scope = this;
			return Vue.http({
				url: phost() + url,
				method: 'POST',
				data: $.extend({
					erptkn: tkn
				}, data)
			});
		},
		actualizar_chosen: function () {
			var formulario = $(this.$el);

			setTimeout(function () {
				formulario.find('select').trigger('chosen:updated');
				$('.chosen-select').trigger('chosen:updated');
			}, 1000);
		},

		cambiarTipo: function (tipo) {
			this.devolucion_alquiler.empezar_desde_id = '';
			if (tipo == 'entrega') {
				var context = this;
				var response = this.ajax('devoluciones_alquiler/ajax-seleccionar-items-entrega');
				response.then(function (response) {

					context.disabledHeaderEmpezableId = false;
					context.empezables = response.data.items;
					context.showNoEntrega = false;

				}, function (response) {});

			}
			if (tipo == "Contrato de alquiler") {
				var context = this;
				var response = this.ajax('devoluciones_alquiler/ajax-seleccionar-items-contrato');
				response.then(function (response) {
					context.disabledHeaderEmpezableId = false;
					context.empezables = response.data.items;
					context.showNoEntrega = true;
				}, function (response) {});
			}

			this.actualizar_chosen();
		},

		limpiarFormularioRetorno: function () {
			$('input, select').attr('value', '');
			this.actualizar_chosen();
		},

		cambiarEmpezable: function (tipo, id) {

			this.guardarBtnDisabled = true;

			//Establecer fecha retorno min
			var entrega = _.find(this.empezables,function(item){
					return item.id==id;
			});

			//ENTREGAS DE ALQUILER
			if(typeof entrega != 'undefined'
			&& typeof entrega.fecha_entrega != 'undefined'
			&& typeof entrega.fecha_entrega.date != 'undefined'
			&& entrega.fecha_entrega.date != ''){
				var minDate = moment(entrega.fecha_entrega.date).format('DD/MM/YYYY H:mm:ss');
			}
			//CONTRATOS DE ALQUILER
			else if(typeof entrega.entregas != 'undefined'){
				var minDate = moment(entrega.entregas[0]["fecha_entrega"]["date"]).format('DD/MM/YYYY H:mm:ss');
			}

			$("#fecha_devolucion").daterangepicker({
					minDate: minDate,
					startDate: moment().format('DD/MM/YYYY H:mm:ss'),
					autoUpdateInput: true,
					timePicker24Hour: true,
					timePicker: true,
					timePickerIncrement: 30,
					singleDatePicker: true,
					showDropdowns: true,
					locale: {
							format: 'DD/MM/YYYY H:mm:ss'
						},
			});

			if (tipo == 'entrega') {

				//Buscar informacion adicional
				var context = this;
				var response = this.ajax('devoluciones_alquiler/ajax-seleccionar-info', {
					id: id,
					tipo: tipo
				});
				response.then(function (response) {

					context.fecha_inicio_contrato = response.data.contrato[0].fecha_inicio_format;
					context.fecha_fin_contrato = response.data.contrato[0].fecha_fin_format;
					context.cliente_id = response.data.contrato[0].cliente_id;
					context.saldo_pendiente_acumulado = response.data.contrato[0].cliente.saldo_pendiente;
					context.credito_favor = response.data.contrato[0].cliente.credito_favor;
					//Vendedor created_by.
					context.vendedor_id=response.data.contrato[0].created_by;
					console.log("retorno vendedor id:", response.data.contrato[0].created_by);

					var itemsdevuelto = 0;
					_.forEach(response.data.items, function(item) {
						itemsdevuelto += _.sumBy(item.contratos_items_detalles_devoluciones, function(o) {
							return parseFloat(o.cantidad);
						});
					});

					if(response.data.items.length > 0){
						context.$broadcast('cambiarEmpezable', response.data.items, id); //contratos_items
					}

					context.actualizar_chosen();
					//Habilitar boton de guardar si existen items para retornar
					context.guardarBtnDisabled = response.data.items.length > 0 && itemsdevuelto < parseInt(response.data.items[0]["entregado"]) ? false : true;

				}, function (response) {});
			} else if (tipo == 'Contrato de alquiler') {

				var context = this;
				var response = this.ajax('devoluciones_alquiler/ajax-seleccionar-info', {
					id: id,
					tipo: tipo
				});
				response.then(function (response) {

					context.fecha_inicio_contrato = response.data.fecha_inicio_format;
					context.fecha_fin_contrato = response.data.fecha_fin_format;
					context.cliente_id = response.data.cliente_id;
					context.saldo_pendiente_acumulado = response.data.cliente.saldo_pendiente;
					context.credito_favor = response.data.cliente.credito_favor;
					context.vendedor_id=response.data.created_by;
					console.log("retorno vendedor id:", response.data.created_by);

					var itemsdevuelto = 0;
					_.forEach(response.data.contratos_items, function(item) {
						itemsdevuelto += _.sumBy(item.contratos_items_detalles_devoluciones, function(o) {
							return parseFloat(o.cantidad);
						});
					});

					if(response.data.contratos_items.length > 0){
						context.$broadcast('cambiarEmpezableContrato', response.data.contratos_items, id); //contratos_items
					}

					//Habilitar boton de guardar si existen items para retornar
					context.guardarBtnDisabled = response.data.contratos_items.length > 0 && itemsdevuelto < parseInt(response.data.contratos_items[0]["entregado"]) ? false : true;

				}, function (response) {});
			}
			this.actualizar_chosen();
		},
		guardar: function () {
			var context = this;
			var formulario = $("#form_devoluciones_alquiler");
			context.guardarBtnDisabled = true;

			if (formulario.validate().form() == false) {
					//mostrar mensaje
					toastr.error('Debe completar los campos requeridos.');
					context.guardarBtnDisabled = false;

			}else{

					formulario.find(':disabled, select, input').removeAttr('disabled');
					context.disabledHeader = false;
					context.disabledEstado = false;

					$('input, select').prop('disabled', false);
					Vue.nextTick(function () {
						context.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Guardando...';
					});
					formulario.submit();
			}

		}

	},
	watch:{
			'devolucion_alquiler.empezar_desde_type' : function (val, oldVal) {
					if (vista=='crear') {
						this.cambiarTipo(val);
					}
			},
			'devolucion_alquiler.empezar_desde_id' : function (val, oldVal) {
					if (vista=='crear') {
						this.cambiarEmpezable(this.devolucion_alquiler.empezar_desde_type, val);
					}
			},
		}
});
