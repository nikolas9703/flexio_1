console.log('items_factura');
Vue.component('items_factura', {
	template: '#items_factura',
	props: {
		factura: Object
	},
	data: function () {
		return {
			currentView: 'items_venta',
                        permiso_editar_precio: editar_precio,
			subtotal: '',
			impuesto: '',
			descuento: 0,
			total: '',
			cobros: 0,
			saldo: '0.00',
			delete_items: [],
			categorias: categorias,
			impuestos: typeof impuestos != 'undefined' ? $.parseJSON(impuestos) : [],
			cuenta_transaccionales: typeof cuenta_transaccionales != 'undefined' ? cuenta_transaccionales : []
		};
	},

	components: {

		'items_venta': require('./../../vue/components/tabla-dinamica-facturas-venta.vue')

	},

	ready: function () {

		var scope = this;

		// si existe variable infofactura
		// cuando es editar factura
		if (typeof infofactura != 'undefined') {

			this.$nextTick(function () {

				//verificar infofactura
				if (typeof infofactura.cobros != 'undefined') {

					//popular cobro si existe
					var cobros = 0;
					infofactura.cobros.forEach(function (cobro) {
						cobros += parseFloat(cobro.monto_pagado);
					});
					scope.cobros = roundNumber(cobros, 2);
				}
			});
		}
	},
	computed: {
		'subtotal': function () {
			var ordendesde = this.$root.$refs.filtro.ordendesde_id;
			var subtotal = 0;

			//
			// Condicion para calcular subtotal
			// cuando se utiliza componente de
			// Contrato de Alquiler.
			//
			if (ordendesde.match(/contrato_alquiler/gi)) {

				var subtotal_items_alquiler = 0;
				var subtotal_items_adicionales = 0;

				// Calculo subtotal - items de alquiler
				if (typeof this.$refs.items.$refs.items != 'undefined') {
					subtotal_items_alquiler = _.sumBy(this.$refs.items.$refs.items.items, function (o) {
						return parseFloat(o.precio_total) || 0;
					});
				}

				// Calculo subtotal - items de cargos adicionales
				if (typeof this.$refs.items.$refs.items_adicionales != 'undefined') {
					subtotal_items_adicionales = _.sumBy(this.$refs.items.$refs.items_adicionales.items, function (o) {
						return parseFloat(o.precio_total) || 0;
					});
				}

				subtotal = subtotal_items_alquiler + subtotal_items_adicionales;

			} else {

				//
				// Calculo de Subtotal para demas Componentes
				//
				subtotal = _.sumBy(this.$refs.items.items, function (o) {
					return parseFloat(o.precio_total) || 0;
				});
			}

			return roundNumber(subtotal, 2);
		},
		'impuesto': function () {
			var ordendesde = this.$root.$refs.filtro.ordendesde_id;
			var impuesto = 0;
			//
			// Condicion para calcular impuesto
			// cuando se utiliza componente de
			// Contrato de Alquiler.
			//
			// si el cliente esta exento de impuesto salir

			if (ordendesde.match(/contrato_alquiler/gi)) {

				var impuesto_items_alquiler = 0;
				var impuesto_items_adicionales = 0;

				// Calculo subtotal - items de alquiler
				if (typeof this.$refs.items.$refs.items != 'undefined') {
					impuesto_items_alquiler = _.sumBy(this.$refs.items.$refs.items.items, function (o) {
						if(typeof porcentaje=='undefined'){
							return;
						}
						return parseFloat((o.precio_total - ((o.descuento / 100) * o.precio_total)) * (porcentaje / 100)) || 0;
					});
				}

				// Calculo subtotal - items de cargos adicionales
				if (typeof this.$refs.items.$refs.items_adicionales != 'undefined') {
					impuesto_items_adicionales = _.sumBy(this.$refs.items.$refs.items_adicionales.items, function (o) {
						if(typeof porcentaje=='undefined'){
							return;
						}
						return parseFloat((o.precio_total - ((o.descuento / 100) * o.precio_total)) * (porcentaje / 100)) || 0;
					});
				}

				impuesto = parseFloat(impuesto_items_alquiler) + parseFloat(impuesto_items_adicionales);

			} else {

				//
				// Calculo de Subtotal para demas Componentes
				//

				impuesto = _.sumBy(this.$refs.items.items, function (o) { //comentario 27 oct
					var impuestofloat = 0;
					var porcentaje = 0;
					if (o.precio_total > 0) {
						if (o.impuesto_porcentaje == 0) {
							$.each(o.impuestos, function (index, value) {
								if (value['id'] == o.impuesto_id) {
									porcentaje = value['impuesto'];
								}
							})
						} else {
							porcentaje = o.impuesto_porcentaje;
						}
						impuestofloat = ((o.precio_total - ((o.descuento / 100) * o.precio_total)) * (porcentaje / 100));
					}
					return parseFloat(impuestofloat);
				});
			}

			return typeof window.impuesto_total != 'undefined' && window.impuesto_total != '' && window.impuesto_total == impuesto ? window.impuesto_total : roundNumber(impuesto, 2);
		},
		'descuento': function () {
			var ordendesde = this.$root.$refs.filtro.ordendesde_id;
			var descuento = 0;

			//
			// Condicion para calcular descuento
			// cuando se utiliza componente de
			// Contrato de Alquiler.
			//
			if (ordendesde.match(/contrato_alquiler/gi)) {

				var descuento_items_alquiler = 0;
				var descuento_items_adicionales = 0;

				// Calculo descuento - items de alquiler
				if (typeof this.$refs.items.$refs.items != 'undefined') {
					descuento_items_alquiler = _.sumBy(this.$refs.items.$refs.items.items, function (o) {
						return parseFloat((o.descuento * o.precio_total) / 100) || 0;
					});
				}

				// Calculo descuento - items de cargos adicionales
				if (typeof this.$refs.items.$refs.items_adicionales != 'undefined') {
					descuento_items_adicionales = _.sumBy(this.$refs.items.$refs.items_adicionales.items, function (o) {
						return parseFloat((o.descuento * o.precio_total) / 100) || 0;
					});
				}

				descuento = parseFloat(descuento_items_alquiler) + parseFloat(descuento_items_adicionales);

			} else {

				//
				// Calculo de Descuento para demas Componentes
				//
				descuento = _.sumBy(this.$refs.items.items, function (o) {
					return parseFloat((o.descuento * o.precio_total) / 100) || 0;
				});
			}

			return roundNumber(descuento, 2);
		},
		'total': function () {
			var estado = '';

			return roundNumber(parseFloat(this.subtotal) + parseFloat(this.impuesto) - parseFloat(this.descuento), 2);

		},
		'saldo': function () {

			if (this.factura.estado_id == 'anulada') {
				return 0;
			} else {
                var aux = typeof window.nota_credito_aprobada !== 'undefined' ? window.nota_credito_aprobada : {total:0};
				return roundNumber(parseFloat((this.total - this.cobros - aux.total)), 2);
			}
		}
	},
	events: {
		cambiarTablaItems: function (tipo_factura) {

			var componente = 'items_venta';
			if (tipo_factura.match(/(orden_venta|contrato_venta)/gi)) {
				componente = 'items_venta';
			} else if (tipo_factura.match(/(contrato_alquiler)/gi)) {
				componente = 'contrato_alquiler';
			}

			this.currentView = componente;
		}
	},
	methods: {
		/*
    	Cambiarlo para trabajar con ajax

    	popularItems: function(e, index, item, items){
    		var scope = this;

    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //
            // Validar Selecion de campo  Lista de precio
            //
            var check = this.verificarSeleccionListaPrecio(index);

            //Obtener categoria seleccionada
            var categoria_id = $(e.currentTarget).find('option:selected').val();

            if(categoria_id=="" || check ==false){
            	item.itemsCat = [];
            	return false;
            }

            var scope = this;
			var response = this.$root.ajax('facturas/ajax-seleccionar-items-por-categoria', {categoria_id: categoria_id});
    		response.then(function (response) {

    			//popular lisatdo de items
    			item.itemsList = !_.isEmpty(response.data.items) ? response.data.items : [];
            });
    	},
    	popularUnidadAtributo: function(e, item, index){

    		e.preventDefault();

            var scope = this;
            var item_id 		= $(e.currentTarget).find('option:selected').val();
            var iteminfo 		= _.find(item.itemsList, function(iteminfo){ return iteminfo.id == item_id; });
            var precio_unidad 	= '';

            //
            // Obtener precio del item
            //
            if(typeof this.factura !== 'undefined'){

        		var precio = _.result(_.find(iteminfo.precios, function(query){
                    return query.id == scope.factura.lista_precio_id;
                }),'pivot');

        		precio_unidad = precio.precio;
            }

            //popular precio unidad
            item.precio_unidad = !_.isEmpty(precio_unidad) ? precio_unidad : '';

            //popular atributos
            item.atributos = !_.isEmpty(iteminfo) ? iteminfo.atributos : [];

            //popular unidades
            item.unidades = !_.isEmpty(iteminfo) && iteminfo.unidades.length > 0 ? iteminfo.unidades : [];

            //Seleccionar impuesto
            item.impuesto_uuid = iteminfo.uuid_venta;

            //Establecer porcentaje de impuesto
            item.impuesto_porcentaje = typeof iteminfo.impuesto != 'undefined' && iteminfo.impuesto != null ? iteminfo.impuesto.impuesto : [];

            item.cuenta_uuid = iteminfo.uuid_ingreso;

            //Calcular Precio Total del Item
            this.calcularPrecioTotal(index);
    	},

    	*/
		popularItems: function (e, index, item, items) {

			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			if (typeof item == 'undefinded') {
				return false;
			}

			var scope = this;
			//
			// Validar Selecion de campo  Lista de precio
			//
			this.verificarSeleccionListaPrecio(index);

			//Obtener categoria seleccionada
			var categoria_id = $(e.currentTarget).find('option:selected').val();

			if (categoria_id == "") {
				item.itemsCat = [];
			}

			var categoria = _.find(this.categorias, function (categoria) {
				return categoria.id == categoria_id;
			});

			item.itemsList = !_.isEmpty(categoria) ? categoria.items : [];
		},
		popularUnidadAtributo: function (e, item, index) {

			if (typeof item == 'undefinded') {
				return false;
			}

			e.preventDefault();

			var scope = this;
			var item_id = $(e.currentTarget).find('option:selected').val();
			var categoria = _.find(scope.categorias, function (categoria) {
				return categoria.id == item.categoria_id;
			});
			var iteminfo = typeof categoria != 'undefined' ? _.find(categoria.items, function (iteminfo) {
				return iteminfo.id == item_id;
			}) : '';
			var precio_unidad = '';

			//
			// Obtener precio del item
			//
			if (typeof this.factura !== 'undefined') {

				var precio = _.result(_.find(iteminfo.precios, function (query) {
					return query.id == scope.factura.lista_precio_id;
				}), 'pivot');

				precio_unidad = precio.precio;
			}

			this.$nextTick(function () {
				item.item_id = item_id;
			});

			//popular precio unidad
			item.precio_unidad = !_.isEmpty(precio_unidad) ? precio_unidad : '';

			//popular atributos
			item.atributos = !_.isEmpty(iteminfo) ? iteminfo.atributos : [];

			//popular unidades
			item.unidades = !_.isEmpty(iteminfo) && iteminfo.unidades.length > 0 ? iteminfo.unidades : [];

			//Seleccionar impuesto
			item.impuesto_uuid = iteminfo.uuid_venta;

			//Seleccionar unidad
			var unidad_id = 0;
			iteminfo.item_unidades.forEach(function (unidad) {
				if (unidad.base == 1) {
					unidad_id = unidad.id_unidad;
				}
			});
			item.unidad_id = unidad_id;

			//Seleccionar cuenta
			item.cuenta_uuid = iteminfo.uuid_ingreso;

			//Establecer porcentaje de impuesto
			item.impuesto_porcentaje = iteminfo.impuesto.impuesto;

			//Calcular Precio Total del Item
			this.calcularPrecioTotal(index);
		},
		calcularPrecioSegunUnidad: function (e, item, index) {

			if (typeof item.unidad == 'undefined') {
				return false;
			}

			var factor_conversion = parseFloat(item.unidad.pivot.factor_conversion);
			var precio_unidad = accounting.toFixed((factor_conversion * item.precio_unidad), 2);
			item.precio_unidad = precio_unidad;

			//Calcular Precio Total del Item
			this.calcularPrecioTotal(index);
		},
		//
		// Verificar si ha seleccionado lista de precio
		// Si no ha seleccionado ninguna opcion
		// resetear seleccion de categoria de item.
		//
		verificarSeleccionListaPrecio: function (index) {

			var scope = this;
			var ordendesde = scope.$root.$refs.filtro.ordendesde_id;

			if (typeof this.factura !== 'undefined') {

				if (this.factura.lista_precio_id === '') {

					//verificar seleccion de lista de precio
					$(this.$root.$el).validate().element('#lista_precio_id');

					//mostrar mensaje
					toastr.warning('Porfavor seleccione Lista de precio.');

					scope.$refs.items.$nextTick(function () {

						if (ordendesde.match(/contrato_alquiler/gi)) {
							// Para Contrato de Alquiler validar lista de pecio
							// de items de cargos adicionales
							scope.$refs.items.$refs.items_adicionales.items[index].categoria_id = '';
							scope.$refs.items.$refs.items_adicionales.items.$set(index, scope.$refs.items.$refs.items_adicionales.items[index]);
						} else {
							scope.$refs.items.items[index].categoria_id = '';
							scope.$refs.items.items.$set(index, scope.$refs.items.items[index]);
						}
					});
					return false;
				}
			}
			return true;
		},
		//
		//Calcular precio total de item
		//
		calcularPrecioTotal: function (index) {

			var scope = this;
			var ordendesde = scope.$root.$refs.filtro.ordendesde_id;
			var precio_unitario = ordendesde.match(/contrato_alquiler/gi) && typeof scope.$refs.items.$refs.items != 'undefined' && typeof scope.$refs.items.$refs.items.items[index] != 'undefined' ? scope.$refs.items.$refs.items.items[index].precio_unidad : (typeof scope.$refs.items.items[index] != 'undefined' ? scope.$refs.items.items[index].precio_unidad : 0);
			var cantidad = ordendesde.match(/contrato_alquiler/gi) && typeof scope.$refs.items.$refs.items != 'undefined' && typeof scope.$refs.items.$refs.items.items[index] != 'undefined' ? scope.$refs.items.$refs.items.items[index].cantidad : (typeof scope.$refs.items.items[index] != 'undefined' ? parseFloat(scope.$refs.items.items[index].cantidad) : 0);
			var precio_total = roundNumber(parseFloat((precio_unitario * cantidad)), 2);

			if (ordendesde.match(/contrato_alquiler/gi)) {
				// Componente de Contrato de Alquiler
				//
				// Calculo para items de alquiler
				// Verificar que exista items de alquiler
				if (typeof scope.$refs.items.$refs.items != 'undefined' && typeof scope.$refs.items.$refs.items.items[index] != 'undefined') {
					scope.$refs.items.$refs.items.items[index].precio_total = precio_total;
					scope.$refs.items.$refs.items.items.$set(index, scope.$refs.items.$refs.items.items[index]);
				}

				// Calculo para items de cargos adicionales
				// Verificar que exista items adicionales
				if (typeof scope.$refs.items.$refs.items_adicionales != 'undefined' && typeof scope.$refs.items.$refs.items_adicionales.items != 'undefined') {

					if (typeof scope.$refs.items.$refs.items_adicionales.items[index] != 'undefined') {

						var precio_unitario = scope.$refs.items.$refs.items_adicionales.items[index].precio_unidad;
						var cantidad = scope.$refs.items.$refs.items_adicionales.items[index].cantidad;
						var precio_total = roundNumber(parseFloat((precio_unitario * cantidad)), 2);

						scope.$refs.items.$refs.items_adicionales.items[index].precio_total = precio_total;
						scope.$refs.items.$refs.items_adicionales.items.$set(index, scope.$refs.items.$refs.items_adicionales.items[index]);
					}
				}
			} else {
				//
				// Calculo para demas Componentes
				//
				scope.$refs.items.$nextTick(function () {
					if (typeof scope.$refs.items != 'undefined' && typeof scope.$refs.items.items[index] != 'undefined') {
						scope.$refs.items.items[index].precio_total = precio_total;
					} else {
						console.log(index, scope.$refs.items.items);
					}
					scope.$refs.items.items.$set(index, scope.$refs.items.items[index]);
				});
			}
		}
	}
});
