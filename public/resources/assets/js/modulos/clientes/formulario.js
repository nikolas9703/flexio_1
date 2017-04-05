
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
            agentes:window.agentes,
            ramos:window.ramos,
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
            agentesCliente:[{agente_id:'', id:'', identificacion:'', no_identificacion:''}],
            ramosCliente:[{ramo_id:'', id:''}],
            //porcentajeCliente:[{porcentaje:'', id:''}],
            agentesRamoCliente: [[{ramos:'', porcentajes:'', id:''}]],
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


            /*$(".iniramo").each(function(){
                var id = $(this).attr("id");
                var idx = id.split("_");
                var valor = $(this).val();
                var valorf = "";
                $.each(valor,function(index, value){
                    valorf = valorf+value+",";
                });
                $("#ramos_h_"+idx[1]+"_"+idx[2]).val(valorf);
                console.log(id, valorf);
            });*/

            var moduloIn = localStorage.getItem("ms-selected");
            if (moduloIn == "seguros" && validaagente == 1) {
                var num = 0;
                $(".selAgente").each(function(){
                    var c = $(this).val();
                    console.log("valor="+c);
                    if (c != "" ) {
                        num=1;
                    }
                });

                if(num>0){
                    this.config.siguiente = !this.config.siguiente;
                }else{
                    toastr.error("Debe seleccionar al menos un Agente.");
                } 
            }else{
                this.config.siguiente = !this.config.siguiente;
            }                       
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

                    $(".iniramo").each(function(){
                        var id = $(this).attr("id");
                        var idx = id.split("_");
                        var valor = $(this).val();
                        var valorf = "";
                        $.each(valor,function(index, value){
                            valorf = valorf+value+",";
                        });
                        $("#ramos_h_"+idx[1]+"_"+idx[2]).val(valorf);
                        console.log(id, valorf);
                    });

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
        'asignados-usuarios': require('./components/asignados.vue'),
        'centros-facturacion': require('./components/centros-facturacion.vue'),
        'formulario-contacto': require('./components/formulario-contacto.vue'),
        'agentesCliente': require('./components/agentes-asignados.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready:function(){
		
		var moduloInicial = localStorage.getItem("ms-selected");
        $("#detalle_unico").val(detalle_unico);
	
		if(moduloInicial=='seguros')
		{
			$('select[name="campo[tipo_identificacion]"] option[value="cedula_nt"]').remove();
			$('select[name="campo[tipo_identificacion]"] option[value="ruc_nt"]').remove();
            $('#agentes_otros').remove();
            $("#correo_cliente").removeAttr("data-rule-required");
            $("#tipo_correo_cliente").removeAttr("data-rule-required");
            $("#span_correo").remove();
		}
        else
        {
            $('select[name="campo[tipo_identificacion]"] option[value="pasaporte"]').remove(); 
            $('#agentes_seguros').remove(); 
        }

        var context = this;

        /*context.agentesRamoCliente = [];
        context.agentesRamoCliente.push([{ramos:'', porcentajes:'', id:''}]);
        console.log(context.agentesRamoCliente);*/

        if(context.config.vista == 'ver'){

            //Oculta Los Agentes para Ver
            $('#agentes_seguros').find('.ibox-content').hide();
            $('#agentes_seguros').find('.fa-chevron-up').addClass('fa-chevron-down');
            $('#agentes_seguros').find('.fa-chevron-down').removeClass('fa-chevron-up');
            //oculta la informacion de pago
            $('#info_pago').find('.ibox-content').hide();
            $('#info_pago').find('.fa-chevron-up').addClass('fa-chevron-down');
            $('#info_pago').find('.fa-chevron-down').removeClass('fa-chevron-up');

            var x = context.detalle.agentesCliente;

            context.detalle = JSON.parse(JSON.stringify(window.cliente));

            console.log("Info  Cliente:");
            console.log(window.cliente);
            //context.detalle.agentesCliente = [];
            if (window.cliente.agentesCliente.length == 0) {
                context.detalle.agentesCliente = [{agente_id:'', id:'', identificacion:'', no_identificacion:''}];
                context.detalle.agentesRamoCliente = [[{ramos:'', porcentajes:'', id:''}]]
            }
            

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
        setTimeout(function(){ 

            $(".selAgente").on('change', function () {

                console.log("hola");
                var idx = $(this).attr("id");
                var x = idx.split("_");

                var id_agente = $("#selAgente_"+x[1]+"").val();

                if (id_agente != "") {
                    //$('select[name="ramos_agentes['+x[1]+'][0]"]').attr("data-rule-required", true);
                    $('.select_ramo_'+x[1]).each(function(){
                        $(this).attr("data-rule-required", true);
                    });
                    //$('input[name="porcentajes_agentes['+x[1]+'][0]"]').attr("data-rule-required", true);
                    $('.input_participacion_'+x[1]).each(function(){
                        $(this).attr("data-rule-required", true);
                    });
                }else{
                    //$('select[name="ramos_agentes['+x[1]+'][0]"]').removeAttr("data-rule-required");
                    $('.select_ramo_'+x[1]).each(function(){
                        $(this).removeAttr("data-rule-required");
                    });
                    //$('input[name="porcentajes_agentes['+x[1]+'][0]"]').removeAttr("data-rule-required");
                    $('.input_participacion_'+x[1]).each(function(){
                        $(this).removeAttr("data-rule-required");
                    });
                }      

                $("#identificacion_agt_"+x[1]+"").val("");
                $("#no_identificacion_agt_"+x[1]+"").val("");

                $.ajax({
                    url: phost() + "clientes/ajax_get_agente",
                    type: "POST",
                    data: {
                        erptkn: tkn,
                        agente: id_agente
                    },
                    dataType: "json",
                    success: function (response) {
                        if (!_.isEmpty(response)) {
                            $("#identificacion_agt_"+x[1]+"").val(response[0].tipo_identificacion);
                            $("#no_identificacion_agt_"+x[1]+"").val(response[0].identificacion);
                        }
                    }
                });
            });
        }, 50);

    },

});

Vue.nextTick(function () {
    form_crear_cliente.guardar();
});





