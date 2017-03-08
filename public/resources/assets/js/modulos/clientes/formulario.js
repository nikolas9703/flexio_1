
Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));

var form_crear_cliente = new Vue({

    el: "#formClienteCrearDiv",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\Cliente\\Models\\Cliente",
            comentable_id: '',

        },

        catalogos:{

            tomas_contacto:window.tomas_contacto,
            tipos_cliente:window.tipos_cliente,
            categorias_cliente:window.categorias_cliente,
            estados_cliente:window.estados_cliente,
            lista_precios_venta:window.lista_precios_venta,
            lista_precios_alquiler:window.lista_precios_alquiler,
            terminos_pago:window.terminos_pago,
            usuarios:window.usuarios,
            provincias:window.provincias,
            distritos:window.distritos,
            corregimientos:window.corregimientos

        },

        detalle:{
            id_cp:'',//se usa para borrar el ciente potenical si aplica
            id:'',
            nombre:'',
            saldo:0,
            credito:0,
            tipo_identificacion:'',
            detalle_identificacion:{tomo:'', folio:'', asiento:'', dv:'', provincia:'', letra:'', pasaporte:''},
            telefonos:[{telefono:'', tipo:'', id:''}],
            correos:[{correo:'', tipo:'', id:''}],
            toma_contacto_id:'',
            tipo:'',
            categoria:'',
            credito_limite:0,
            comentario:'',
            estado:'por_aprobar',
            exonerado_impuesto:'',
            retiene_impuesto:'no',
            lista_precio_venta_id:'',
            lista_precio_alquiler_id:'',
            termino_pago:'',
            agentesCliente:[{usuario_id:'', linea_negocio:'', id:''}],
            centros_facturacion:[{direccion:'', nombre:'', provincia_id: '', distrito_id: '', corregimiento_id: '', id:''}]
        },

        contacto:{
            id:'',
            nombre:'',
            cargo:'',
            telefono:'',
            celular:'',
            correo:'',
            direccion:'',
            comentario:'',
            principal: '',
            cliente_id: '',
            estado:'activo',
            tipo_identificacion:'',
            detalle_identificacion:{tomo:'', folio:'', asiento:'', dv:'', provincia:'', letra:'', pasaporte:''}
        },

        config: {

            vista: window.vista,
            select2:{width:'100%'},
            datepicker2:{dateFormat: "dd/mm/yy"},
            inputmask:{

                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2:{
                    alias:'currency',
                    prefix: "",
                    autoUnmask : true,
                    removeMaskOnSubmit: true
                }

            },
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableWatch:true,
            siguiente:false,
            exonerado:false,
            showFormContacto:false

        }

    },

    methods:{

        toggleCentroFacturacion: function(){
            this.config.siguiente = !this.config.siguiente;
        },

        showAgregarCentroFacturacion: function(){
            this.config.siguiente = true;
        },

        showAgregarContacto: function(){
            this.config.showFormContacto = true;
        },

        eliminarCentroFacturacion: function(row){
            var context = this;
            var centro_facturacion_id = $(row).data('id');
            var centro_facturacion = _.find(context.detalle.centros_facturacion, function(o){
                return o.id == centro_facturacion_id;
            });

            context.detalle.centros_facturacion.$remove(centro_facturacion);
            $.ajax({
                url: phost() + "clientes/ajax-eliminar-centro",
                type: "POST",
                data: {
                    erptkn: tkn,
                    centro_facturacion_id: $(row).data('id')
                },
                dataType: "json",
                success: function (response) {
                    //Recargar tabla de contactos
                    $("#tablaClientesCentrosFacturacionGrid").trigger('reloadGrid');
                }
            });
        },

        populateContacto: function(row){
            var context = this;
            $('#optionsModal').modal('hide');
            $.ajax({
                url: phost() + "contactos/ajax-contacto-info",
                type: "POST",
                data: {
                    erptkn: tkn,
                    uuid_contacto: $(row).data('uuid_contacto')
                },
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        context.contacto = response;
                        context.config.showFormContacto = true;
                    }
                }
            });
        },

        populateClientePotencial: function(uuid_cliente_potencial){
            var context = this;
            $.ajax({
                url: phost() + "clientes_potenciales/ajax-cliente-potencial-info",
                type: "POST",
                data: {
                    erptkn: tkn,
                    uuid_cliente_potencial: uuid_cliente_potencial
                },
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        console.log('pase');
                        context.detalle = $.extend(context.detalle, response);
                    }
                }
            });
        },

        guardar: function () {
            var context = this;
            var $form = $("#formClienteCrear");

            $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    toastr['error']('Los campos marcados con asterisco (*) son requeridos');
                },
                submitHandler: function (form) {
                    $('input, select, input:text').prop('disabled', false);
					$('input:text,input:hidden, select:hidden, textarea, select').removeAttr('disabled');
					$('input,input:text,input:hidden, select:hidden, textarea, select').attr('disabled',false);
                    $('form').find(':submit').prop('disabled',true);
					
                    form.submit();
                }
            });
        }

    },

    components:{

        'datos-cliente': require('./components/datos-cliente.vue'),
        'informacion-pago': require('./components/informacion-pago.vue'),
        'asignados': require('./components/agentes-asignados.vue'),
        'centros-facturacion': require('./components/centros-facturacion.vue'),
        'formulario-contacto': require('./components/formulario-contacto.vue'),
        'agentesCliente': require('./components/agentes-asignados.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready:function(){
		
		var moduloInicial = localStorage.getItem("ms-selected");
	
		if(moduloInicial=='seguros')
		{
			$('select[name="campo[tipo_identificacion]"] option[value="cedula_nt"]').remove();
			$('select[name="campo[tipo_identificacion]"] option[value="ruc_nt"]').remove();
		}

        var context = this;

        if(context.config.vista == 'ver'){

            context.detalle = JSON.parse(JSON.stringify(window.cliente));
            Vue.nextTick(function(){

                if(window.desde_modal_cliente == 'agregar_contacto'){
                    context.showAgregarContacto();
                }else if (window.desde_modal_cliente == 'agregar_centro_facturacion') {
                    context.showAgregarCentroFacturacion();
                }

                context.contacto.cliente_id = context.detalle.id;
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.cliente.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.cliente.id));
                context.disableWatch = false;

                if(context.detalle.exonerado_impuesto.length)
    	        {
    	            context.config.exonerado=true;
    	        }

                $("#moduloOpciones").on("click", "#agregarContactoBtn", function(e){
                    context.contacto.id = '';
                    context.showAgregarContacto();
                });

                $("#moduloOpciones").on("click", "#agregarCentroFacturacionBtn", function(e){
                    context.showAgregarCentroFacturacion();
                });

                $('body').on("click", ".clienteVerContacto", function(e){
                    context.populateContacto(this);
                });

                $('body').on("click", ".verCentroFacturacion", function(e){
                    context.showAgregarCentroFacturacion();
                    $('#optionsModal').modal('hide');
                });

                $('body').on("click", ".eliminarCentroFacturacion", function(e){
                    context.eliminarCentroFacturacion(this);
                    $('#optionsModal').modal('hide');
                });

            });

        }else{
            if(window.desde_modal_cliente == 'cliente_potencial'){
                context.populateClientePotencial(window.desde_modal_cliente_ref);
            }
        }

    },

});

Vue.nextTick(function () {
    form_crear_cliente.guardar();
});


