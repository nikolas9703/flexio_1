//Directiva para campos Chosen
Vue.directive('chosen', {
    twoWay: true,
    bind: function () {
    	var scope = this;
        var formulario = $(scope.$el);

        setTimeout(function() {
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
        setTimeout(function(){
        	$(scope.el).trigger("chosen:updated");
        },500);
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
      onClose: function( selectedDate ) {}
    });
  },
  update: function (val) {
    $(this.el).datepicker('setDate', val);
  }
});

var listaItems = [{id: '', categoria_id:'', cantidad:1, impuesto_uuid:'', cuenta_uuid: '', atributo_id: '', descuento: 0, impuesto_porcentaje:'', precio_unidad:'', precio_total:'', impuestos: typeof impuestos != 'undefined' ? impuestos : [], items: [], atributos:[],  unidades:[], cuentas: typeof cuentas != 'undefined' ? cuentas : []}];
var listaServicios = typeof listaServicios != 'undefined' && listaServicios !== '' ? listaServicios : [{id: '', categoria_id:'', item_id: '', serie_id: '', equipo_id: '', itemseleccionado:'', verificando_capacidad: '', itemsservicio:[], items:listaItems, series:[]}];



Vue.http.options.emulateJSON = true;
var ODTModel = new Vue({
    el: '#ordenTrabajoForm',
    data: {
    	modal: {titulo:'', contenido:'', footer:''},
    	id: typeof id != 'undefined' && _.isNumber(id) ? id : '',
    	orden_de: typeof orden_de != 'undefined' ? orden_de : '',
    	orden_de_id: typeof orden_de_id != 'undefined' && _.isNumber(orden_de_id) ? orden_de_id : '',
    	ordenDeIdOptions: typeof ordenDeIdArray != 'undefined' ? ordenDeIdArray : [],
    	cliente_id: typeof cliente_id != 'undefined' && _.isNumber(cliente_id) ? cliente_id : '',
    	estado_id: typeof estado_id != 'undefined' && _.isNumber(estado_id) ? estado_id : 11,
    	tipo_orden_id: typeof tipo_orden_id != 'undefined' && _.isNumber(tipo_orden_id) ? tipo_orden_id : '',
    	lista_precio_id: typeof lista_precio_id != 'undefined' && _.isNumber(lista_precio_id) ? lista_precio_id : listaPrecioIdDefault,
    	lista_precio_alquiler_id: typeof lista_precio_alquiler_id != 'undefined' && _.isNumber(lista_precio_alquiler_id) ? lista_precio_alquiler_id : listaPrecioAlquilerIdDefault,
    	facturable_id: typeof facturable_id != 'undefined' && _.isNumber(facturable_id) ? facturable_id : '',
    	centro_contable_id: typeof centro_id != 'undefined' && _.isNumber(centro_id) ? centro_id : '',
    	bodega_id: typeof bodega_id != 'undefined' && _.isNumber(bodega_id) ? bodega_id : '',
    	comentario: typeof comentario != 'undefined' && _.isString(comentario) ? comentario : '',
    	credito_favor: '',
    	saldo_pendiente_acumulado: '',
    	fecha_inicio: typeof fecha_inicio != 'undefined' && _.isString(fecha_inicio) ? fecha_inicio : moment().format('DD/MM/YYYY'),
    	fecha_planificada_fin: typeof fecha_planificada_fin != 'undefined' && _.isString(fecha_planificada_fin) ? fecha_planificada_fin : '',
    	fecha_real_fin: typeof fecha_real_fin != 'undefined' && _.isString(fecha_real_fin) ? fecha_real_fin : '',
    	ordenDesdeOptions: _.isUndefined(window.ordenDesdeArray)? [] : window.ordenDesdeArray,
    	clienteOptions: typeof clientesArray != 'undefined' ? clientesArray : [],
    	estadosOptions: typeof estadosArray != 'undefined' ? $.parseJSON(estadosArray) : [],
    	tiposOrdenOptions: typeof tiposOrdenArray != 'undefined' ? $.parseJSON(tiposOrdenArray) : [],
    	listaTipoPrecioOptions: typeof listaTipoPrecioArray != 'undefined' ? $.parseJSON(listaTipoPrecioArray) : [],
    	listaFacturableOptions: typeof listaFacturableArray != 'undefined' ? $.parseJSON(listaFacturableArray) : [],
    	listaCentrosOptions: typeof listaCentrosArray != 'undefined' ? $.parseJSON(listaCentrosArray) : [],
    	listaBodegasOptions: typeof listaBodegasArray != 'undefined' ? $.parseJSON(listaBodegasArray) : [],
    	listaservicios: listaServicios,
        listarItems: listaItems,
    	categorias: typeof categorias != 'undefined' && !_.isString(categorias) ? $.parseJSON(categorias) : [],
    	subtotal: 0,
        descuento: 0,
        impuesto: 0,
        total: 0,
        cobros: 0,
        saldo: 0,
        delete_items:[],
        delete_servicios: [],
        guardarBtn: 'Guardar',
    	guardarBtnDisabled: false,
        mostrarCategoriaServicio:true
    },
    created: function () { },
    ready: function () {

        var scope = this;
        var formulario = $(scope.$el);
       // this.tipoSevicioSelect(this.orden_de_id);
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
            setTimeout(function(){
	            if(!_.isUndefined(cliente_id)) {
	            	$('#cliente_id').trigger('change');
	        	}
	            scope.actualizar_chosen();
            },1000);
        });
        scope.actualizar_chosen();

    },
    methods: {
    	ajax: function(url, data) {
			var scope = this;
			return Vue.http({
                url: phost() + url,
                method: 'POST',
                data: $.extend({erptkn: tkn}, data)
            });
		},
    	agregarServicio: function(e){
            var scope =  this;
            this.$nextTick(function () {
            	scope.listaServicios.push({id: '', categoria_id:'', item_id: '', serie_id: '', equipo_id: '', itemseleccionado:'', items:[], series:[]});
            });
    	},
    	actualizar_chosen: function(){
    		var formulario = $(this.$el);
    		setTimeout(function(){
             	formulario.find('select').trigger('chosen:updated');
             	$('.chosen-select').trigger('chosen:updated');
            },1000);
        },
    	popularOrdenDeId: function(evt, params) {

    		if(typeof params == 'undefined') {
                return false;
            }

    		if(params.selected.match(/cliente/gi)){
    			this.ordenDeIdOptions = clientesArray;
    		}
    		this.actualizar_chosen();
        },
        verficarSeleccion: function(evt, params){

        	this.orden_de_id = params.selected;

        	//Clientes
        	if(this.orden_de == "clientes"){

        		setTimeout(function(){
        			$('#cliente_id').trigger('change');
        		},100);

        		this.cliente_id = this.orden_de_id;
    		}

        	this.actualizar_chosen();
        },
        popularDatosCliente1: function(e){
        	e.preventDefault();
          e.returnValue = false;
          e.stopPropagation();

          var cliente_id = $(e.currentTarget).find('option:selected').val();
          console.log(cliente_id);

          if(cliente_id===''){
            	this.saldo_pendiente_acumulado = '';
                this.credito_favor = '';
          }

            var cliente = _.find(this.clienteOptions, function(query){
            	return query.id == cliente_id;
            });

            if(_.isUndefined(cliente)){
            	this.saldo_pendiente_acumulado = '';
                this.credito_favor = '';
            	return false;
            }

            this.saldo_pendiente_acumulado = _.isEmpty(cliente['saldo_pendiente']) ? '0.00': cliente['saldo_pendiente'];
            this.credito_favor = _.isEmpty(cliente['credito_favor']) ? '0.00' : cliente['credito_favor'];
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

            this.guardarBtn =  '<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Guardando...';
        	this.guardarBtnDisabled = true;

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

            if(tipo === '1'){
                this.mostrarCategoriaServicio = false;
            }else{
                this.mostrarCategoriaServicio = true;
            }

            //return true;
        }
    },
    computed:{
    	'subtotal': function(){
    		var subtotal = 0;
    		this.listaservicios.forEach(function(servicio) {
				subtotal += _.sumBy(servicio.items, function(o){
        			return o.precio_total !== '' ? parseFloat(o.precio_total) || 0 : 0;
        	    });
    		});
    		return roundNumber(subtotal,2);
    	},
        'descuento': function(){
       	   var descuento = 0;

    		this.listaservicios.forEach(function(servicio) {
				descuento += _.sumBy(servicio.items, function(o){
         			 return o.descuento !== '' ? parseFloat(o.descuento/100*o.precio_total) || 0 : 0;
        	    });
    		});

    	   return roundNumber(descuento,2);
    	},

		'impuesto': function(){
			var impuesto = 0;
			this.listaservicios.forEach(function(servicio) {
				impuesto += _.sumBy(servicio.items, function(o){
        			return o.precio_total !== '' ? parseFloat((o.impuesto_porcentaje * o.precio_total)/100) || 0 : 0;
        	    });
    		});
			return roundNumber(impuesto,2);
		},
    	'total': function(){
    		return roundNumber(parseFloat(this.subtotal) + parseFloat(this.impuesto), 2);
    	},
    	'saldo': function(){
    		return roundNumber(parseFloat((this.total - this.cobros)),2);
    	}
    }
});
