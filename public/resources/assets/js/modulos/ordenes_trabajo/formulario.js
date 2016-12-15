// jshint esversion:6
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
			});
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
Vue.transition('listado', {
	enterClass: 'fadeIn',
	leaveClass: 'fadeOut'
});

var listaItems = [{
	id: '',
	categoria_id: '',
	cantidad: 1,
	impuesto_uuid: '',
	cuenta_uuid: '',
	atributo_id: '',
	descuento: 0,
	impuesto_porcentaje: '',
	precio_unidad: '',
	precio_total: '',
	impuestos: typeof impuestos != 'undefined' ? impuestos : [],
	items: [],
	atributos: [],
	unidades: [],
	cuentas: typeof cuentas != 'undefined' ? cuentas : []
}];
var listaServicios = typeof serviciosCollection != 'undefined' && serviciosCollection !== '' ? serviciosCollection : [{
	id: '',
	categoria_id: '',
	item_id: '',
	serie_id: '',
	equipo_id: '',
	itemseleccionado: '',
	verificando_capacidad: '',
	itemsservicio: [],
	items: listaItems,
	series: []
}];
//Vue.http.options.emulateJSON = true;
import items from './../../config/lines_items';
var ODTModel = new Vue({
	el: '#ordenTrabajoForm',
	data: {
		modal: {
			titulo: '',
			contenido: '',
			footer: ''
		},
		id: typeof id != 'undefined' && _.isNumber(id) ? id : '',
		orden_de: typeof orden_de != 'undefined' ? orden_de : '',
		orden_de_id: typeof orden_de_id != 'undefined' && _.isNumber(orden_de_id) ? orden_de_id : '',
		ordenDeIdOptions: typeof ordenDeIdArray != 'undefined' ? ordenDeIdArray : [],
		cliente_id: typeof cliente_id != 'undefined' && _.isNumber(cliente_id) ? cliente_id : '',
		estado_id: typeof estado_id != 'undefined' && _.isNumber(estado_id) ? estado_id : 11,
		tipo_orden_id: typeof tipo_orden_id != 'undefined' && _.isNumber(tipo_orden_id) ? tipo_orden_id : '',
		lista_precio_id: typeof lista_precio_id != 'undefined' && _.isNumber(lista_precio_id) ? lista_precio_id : listaPrecioIdDefault,
		//lista_precio_id: aux_dev_ivan.lista_precio_id,
		facturable_id: typeof facturable_id != 'undefined' && _.isNumber(facturable_id) ? facturable_id : '',
		centro_contable_id: typeof centro_id != 'undefined' && _.isNumber(centro_id) ? centro_id : '',
		bodega_id: typeof bodega_id != 'undefined' && _.isNumber(bodega_id) ? bodega_id : '',
		comentario: typeof comentario != 'undefined' && _.isString(comentario) ? comentario : '',
		credito_favor: '',
		saldo_pendiente_acumulado: '',
		fecha_inicio: typeof fecha_inicio != 'undefined' && _.isString(fecha_inicio) ? fecha_inicio : moment().format('DD/MM/YYYY'),
		fecha_planificada_fin: typeof fecha_planificada_fin != 'undefined' && _.isString(fecha_planificada_fin) ? fecha_planificada_fin : '',
		fecha_real_fin: typeof fecha_real_fin != 'undefined' && _.isString(fecha_real_fin) ? fecha_real_fin : '',
		ordenDesdeOptions: typeof ordenDesdeArray != 'undefined' ? ordenDesdeArray : [],
		clienteOptions: typeof clientesArray != 'undefined' ? clientesArray : [],
		estadosOptions: typeof estadosArray != 'undefined' ? $.parseJSON(estadosArray) : [],
		tiposOrdenOptions: typeof tiposOrdenArray != 'undefined' ? $.parseJSON(tiposOrdenArray) : [],
		listaTipoPrecioOptions: typeof listaTipoPrecioArray != 'undefined' ? $.parseJSON(listaTipoPrecioArray) : [],
		listaFacturableOptions: typeof listaFacturableArray != 'undefined' ? $.parseJSON(listaFacturableArray) : [],
		listaCentrosOptions: typeof listaCentrosArray != 'undefined' ? $.parseJSON(listaCentrosArray) : [],
		listaBodegasOptions: typeof listaBodegasArray != 'undefined' ? $.parseJSON(listaBodegasArray) : [],
		lista_servicios: listaServicios,
		listarItems: listaItems,
		OrdenesVentas: !_.isUndefined(window.ordenes_ventas)?window.ordenes_ventas:[],
		categorias: typeof categoriasItems != 'undefined' ? categoriasItems : [],
		subtotal: 0,
		descuento: 0,
		impuesto: 0,
		total: 0,
		cobros: 0,
		saldo: 0,
		delete_items: [],
		delete_servicios: [],
		guardarBtn: 'Guardar',
		guardarBtnDisabled: false,
		mostrarCategoriaServicio: true,

		config: {

			vista: window.vista,
			enableWatch: false,
			select2: {
				width: '100%'
			},

			inputmask: {
				cantidad: {
					'mask': '9{1,4}',
					'greedy': false
				},
				descuento: {
					'mask': '9{1,2}[.9{0,2}]',
					'greedy': false
				},
				currency: {
					'mask': '9{1,8}[.9{0,2}]',
					'greedy': false
				},
				currency2: {
					'mask': '9{1,8}[.9{0,4}]',
					'greedy': false
				}

			},
			disableEmpezarDesde: false,
			disableDetalle: false,
			disableArticulos: false,
			modulo: 'cotizaciones', //'ordenes_trabajo',
			editarPrecio: window.editar_precio
		},

		catalogos: {
			clientes: window.clientes,
			terminos_pago: window.terminos_pago,
			vendedores: window.vendedores,
			precios: window.precios,
			centros_contables: window.centros_contables,
			bodegas: window.bodegas,
			categorias: window.categoriasItems,
			cuentas: window.cuenta,
			impuestos: window.impuesto,
			usuario_id: window.usuario_id,
			aux: {}
		},
		detalle: {
			id: '',
			termino_pago: 'al_contado',
			saldo_cliente: 0,
			credito_cliente: 0,
			fecha_desde: moment().format('DD/MM/YYYY'),
			fecha_hasta: moment().add(30, 'days').format('DD/MM/YYYY'),
			creado_por: '',
			item_precio_id: '',
			centro_contable_id: '',
			centro_facturacion_id: '',
			centros_facturacion: [],
			bodega_id: '',
			estado: 'abierta',
			observaciones: '',
			articulos: [{
				id: '',
				cantidad: '',
				categoria_id: '',
				cuenta_id: '',
				cuentas: '[]',
				descuento: '',
				impuesto_id: '',
				item_id: '',
				item_hidden_id: '',
				items: [],
				precio_total: '',
				precio_unidad: '',
				precios: [],
				unidad_id: '',
				unidad_hidden_id: '',
				unidades: [],
				descripcion: '',
				facturado: false,
				atributos: [],
				atributo_text: '',
				atributo_id: ''
			}]

		},
		empezable: {
			label: 'Empezar orden de venta desde',
			type: '',
			types: [{
				id: 'cotizacion',
				nombre: 'Cotizaci&oacute;n'
			}],
			id: '',
			cotizacions: window.cotizaciones
		},
		itemsStorage:items,
		clienteCentroFacturable:[],
		equiposTrabajo:!_.isUndefined(window.listaEquiposTrabajoArray)?window.listaEquiposTrabajoArray:[],
		centro_facturable_id:'',
		equipo_trabajo_id:''
	},
	components: {
		'articulos': require('./../../vue/components/tabla-dinamica.vue'),
		'totales': require('./../../vue/components/tabla-totales.vue')
	},
	created: function () {},
	ready: function () {
		var scope = this;
		var formulario = $(scope.$el);

		scope.detalle.item_precio_id = scope.lista_precio_id;

		if (scope.config.vista == 'ver') {
			this.ordenDeIdOptions = window.orden_de  ==='clientes'?this.clienteOptions:this.OrdenesVentas;
			Vue.nextTick(function () {

				$('#orden_de_id').val(scope.orden_de_id);
				scope.fillEditable();
				//scope.detalle = $.extend(scope.detalle, JSON.parse(JSON.stringify(window.orden_trabajo)));
				scope.actualizar_chosen();
				Vue.nextTick(function () {

					scope.config.enableWatch = true;

				});

			});
		}
		if (scope.id !== "") {
			scope.filtrarEstadoSegunFacturacion();
			scope.popularDatosCliente();
		}

		this.$nextTick(function () {
			//Mostrar formulario
			$('div.loader').remove();
			formulario.removeClass('hide').addClass('fadeIn');

			//Al cambiar dropdowns chosen
			$('#orden_de').on('change', this.popularOrdenDeId);
			$('#orden_de_id').on('change', this.verficarSeleccion);
			$('#cliente_id').on('change', this.popularDatosCliente);

			//Validacion jQuery Validate
			$.validator.setDefaults({
				errorPlacement: function (error, element) {
					return true;
				}
			});
			$(formulario).validate({
				focusInvalid: true,
				ignore: '',
				wrapper: ''
			});

			//Si existe variable
			setTimeout(function () {
				if (!_.isUndefined(cliente_id)) {
					$('#cliente_id').trigger('change');
				}
				scope.actualizar_chosen();
			}, 1000);
		});
		scope.actualizar_chosen();

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
		agregarServicio: function (e) {
			var scope = this;
			this.$nextTick(function () {
				scope.listaServicios.push({
					id: '',
					categoria_id: '',
					item_id: '',
					serie_id: '',
					equipo_id: '',
					itemseleccionado: '',
					items: [],
					series: []
				});
			});
		},
		actualizar_chosen: function () {
			var formulario = $(this.$el);
			setTimeout(function () {
				formulario.find('select').trigger('chosen:updated');
				$('.chosen-select').trigger('chosen:updated');
			}, 1000);
		},
		popularOrdenDeId: function (evt, params) {

			if (typeof params == 'undefined') {
				return false;
			}

			if(!_.isEmpty(params.selected)){
				this.ordenDeIdOptions = params.selected ==='clientes'?clientesArray:this.OrdenesVentas;
			}

			this.actualizar_chosen();
		},
		verficarSeleccion: function (evt, params) {

			this.orden_de_id = params.selected;

			//Clientes
			if (!_.isEmpty(this.orden_de)) {

				setTimeout(function () {
					$('#cliente_id').trigger('change');
				}, 100);

				this.cliente_id = this.orden_de_id;
				if(this.orden_de ==='orden_venta' && !_.isEmpty(this.orden_de_id)){
					//hacer post
					var ordenSelecionada = _.find(this.ordenDeIdOptions,(ord)=> ord.id == this.orden_de_id);
					this.popularFormulario(ordenSelecionada);
				}
			}

			this.actualizar_chosen();
		},
		popularDatosCliente: function (e) {
			var field = '';
			if (typeof e != 'undefined') {
				e.preventDefault();
				e.returnValue = false;
				e.stopPropagation();
				field = e.currentTarget;
			} else {
				field = "#cliente_id";
			}

			var scope = this;

			var cliente_id = $(field).find('option:selected').val();

			if (cliente_id === '') {
				this.saldo_pendiente_acumulado = '';
				this.credito_favor = '';
			}

			var cliente = _.find(this.clienteOptions, function (query) {
				return query.id == cliente_id;
			});

			if (_.isUndefined(cliente)) {
				this.saldo_pendiente_acumulado = '';
				this.credito_favor = '';
				return false;
			}

			this.$nextTick(function () {
				scope.saldo_pendiente_acumulado = cliente['saldo_pendiente'];
				scope.credito_favor = cliente['credito_favor'];
			});
		},
		postAjax:function(ajaxUrl, datos){
          return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
	    },
		guardar: function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			var scope = this;
			var formulario = $(scope.$el);

			if (formulario.validate().form() === false) {
				//mostrar mensaje
				toastr.error('Debe completar los campos requeridos.');
				return false;
			}

			formulario.find(':disabled').removeAttr('disabled');
			//toastr.info('<h3><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Guardando...</h3>', '', {toastClass:'navy-bg', iconClass: 'in', progressBar:false, extendedTimeOut:60});

			this.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Guardando...';
			this.guardarBtnDisabled = true;
			$('#tipo_orden_id').prop("disabled", false);
			$('#cliente_id').prop("disabled", false);
			$('#facturable_id').prop("disabled", false);
			Vue.http({
				url: phost() + 'ordenes_trabajo/ajax-guardar-orden',
				method: 'POST',
				headers: {
					erptkn: tkn,
				},
				data: formulario.serializeObject()
			}).then(function (response) {
				// success callback

				//Check Session
				if ($.isEmptyObject(response.data.session) === false) {
					window.location = phost() + "login?expired";
				}

				//Verificar si el formulario esta siendo usado desde
				//Ver Detalle de Colaborador
				if (window.location.href.match(/(colaboradores)/g)) {

				} else {
					if (response.data.guardado === true) {
						window.location = phost() + 'ordenes_trabajo/listar';
					}
				}

			}, function (response) {
				// error callback
			});
		},
		tipoSevicioSelect: function (tipo) {

			var tipo2 = $('#tipo_orden_id').find('option:selected').val();

			if (tipo === '1') {
				this.mostrarCategoriaServicio = false;
			} else {
				this.mostrarCategoriaServicio = true;
			}
			//return true;
		},
		filtrarEstadoSegunFacturacion: function () {
			var scope = this;
			var selected = _.find(this.listaFacturableOptions, function (query) {
				return query.id == scope.facturable_id;
			});

			if (selected.nombre.match(/no/gi)) {
				var estados = _.filter(this.estadosOptions, function (o) {
					return !o.nombre.match(/factura/gi);
				});
				//Todos los estados
				Vue.nextTick(function () {
					scope.estadosOptions = estados;
				});
				return false;
			}

			//Todos los estados
			Vue.nextTick(function () {
				scope.estadosOptions = $.parseJSON(estadosArray);
			});
			return false;
		},
		popularFormulario(ordenes){

			this.cliente_id = ordenes.cliente_id;
			this.credito_favor = ordenes.cliente.credito_favor;
			this.tipo_orden_id = 1;
			this.tipoSevicioSelect(1);
			this.saldo_pendiente_acumulado = ordenes.cliente.saldo_pendiente;
			this.centro_contable_id = ordenes.centro_contable_id;
			this.lista_precio_id = ordenes.item_precio_id;
			this.bodega_id = ordenes.bodega_id;
			this.clienteCentroFacturable = ordenes.cliente.centro_facturable;
			this.centro_facturable_id = ordenes.centro_facturacion_id;
			this.facturable_id = 10;
			Vue.nextTick(function(){

				this.lista_precio_id = ordenes.item_precio_id;
			});

			ordenes.items.forEach(function(item){
		      item.atributos = item.item.atributos || [];
		      item.item_hidden_id = item.item_id;
		      item.nombre = item.item.nombre;
			  item.cuentas = [];
			  item.unidad_hidden_id = item.unidad_id;
			  item.unidades = item.item.unidades;
		    });
			this.detalle.articulos = ordenes.items;
			this.itemsStorage = ordenes.items;
		},
		fillEditable(){
			if((this.orden_de ==='orden_venta' || this.orden_de ==='clientes' ) && this.tipo_orden_id === 1){

				var datos = !_.isUndefined(window.martillazo)?window.martillazo:[];
				if(!_.isEmpty(datos)){

					datos.items.forEach(function(item){
				      item.atributos = item.item.atributos || [];
				      item.item_hidden_id = item.item_id;
				      item.nombre = item.item.nombre;
					  item.cuentas = [];
					  item.unidad_hidden_id = item.unidad_id;
					  item.unidades = item.item.unidades;
				    });
					this.clienteCentroFacturable = datos.cliente.centro_facturable;
					this.centro_facturable_id = datos.centro_facturable_id;
					this.equipo_trabajo_id = datos.equipo_trabajo_id;
					this.detalle.articulos = datos.items;
					this.itemsStorage = datos.items;
				}
			}else{
				this.detalle = $.extend(this.detalle, JSON.parse(JSON.stringify(window.orden_trabajo)));
			}
		},
		CambiarEstado(estado_id){

			if(estado_id === 16 && _.isEmpty(this.fecha_real_fin)){
				this.guardarBtnDisabled = true;
			}else{
				this.guardarBtnDisabled = false;
			}
		}

	},
	watch: {
		'facturable_id': function (val, oldVal) {
			this.filtrarEstadoSegunFacturacion();
		},'fecha_real_fin':function(val,oldVal){
			if(!_.isEmpty(val)){
				this.guardarBtnDisabled = false;
			}
		}
	},
	computed: {

		'subtotal': function () {
			var subtotal = 0;
			this.lista_servicios.forEach(function (servicio) {
				subtotal += _.sumBy(servicio.items, function (o) {
					return o.precio_total !== '' ? parseFloat(o.precio_total) || 0 : 0;
				});
			});
			return roundNumber(subtotal, 2);
		},
		'descuento': function () {
			var descuento = 0;

			this.lista_servicios.forEach(function (servicio) {
				descuento += _.sumBy(servicio.items, function (o) {
					return o.descuento !== '' ? parseFloat(o.descuento / 100 * o.precio_total) || 0 : 0;
				});
			});

			return roundNumber(descuento, 2);
		},

		'impuesto': function () {
			var impuesto = 0;
			this.lista_servicios.forEach(function (servicio) {
				impuesto += _.sumBy(servicio.items, function (o) {
					return o.precio_total !== '' ? parseFloat((o.impuesto_porcentaje * o.precio_total) / 100) || 0 : 0;
				});
			});
			return roundNumber(impuesto, 2);
		},
		'total': function () {
			return roundNumber(parseFloat(this.subtotal) + parseFloat(this.impuesto), 2);
		},
		'saldo': function () {
			return roundNumber(parseFloat((this.total - this.cobros)), 2);
		},
		soloOrdenesVentas(){
			return this.orden_de ==='orden_venta' && this.tipo_orden_id === 1;
		},
		disabledCampoCliente(){
			return true;
		},
		filtroEquipotrabajo(){
			return $.parseJSON(this.equiposTrabajo);
		}
	}
});
