

var formularioCrearCotizacionAlquiler = new Vue({
    
    el: '#form_crear_cotizacion_alquiler',
    
    ready: function ()
    {
        var context = this;
        
        if(context.vista == 'editar')
        {
            var cotizacion_alquiler_json = JSON.parse(cotizacion_alquiler);
            context.cotizacion_alquiler = cotizacion_alquiler_json;
            context.disabledEstado = false;
            
            if(cotizacion_alquiler_json.estado_id != 'por_aprobar')//distinto de por aprobar
            {
                context.disabledEditar = true;
            }
        }
    },
    
    data: {
        vista: vista,
        disabledHeader: false,
        disabledEstado: true,
        disabledEditar: false,
        
        config:{
            
            datepicker2:{
                
                dateFormat: "dd/mm/yy",
                
                onClose: function( selectedDate ) {
                    
                    var id = $(this).prop('id');
                    if(id ==='fecha_emision'){
                        $("#valido_hasta").datepicker( "option", "minDate", selectedDate );
                    }
                    if(id ==='valido_hasta'){
                        $("#fecha_emision").datepicker( "option", "maxDate", selectedDate );
                    }
                    
                }
                
            }
            
        },
        

        cotizacion_alquiler: {
            id:'',
            empezar_desde_type: '',
            empezar_desde_id: '',
            codigo: codigo,
            centros_facturacion: [],
            centro_facturacion_id: '',
            lista_precio_id:'',
            centro_contable_id:'',
            termino_pago_id:'',
            saldo: '',
            credito: '',
            vendedor_id: '',
            estado_id: 'por_aprobar'//por aprobar
        },
        
        //catalogos
        clientes: clientes, //catalogos from controller
        clientes_potenciales: clientes_potenciales, //catalogos from controller
        vendedores: vendedores, //catalogos from controller
        estados: estados, //catalogos from controller
        terminos_pago: terminos_pago, //catelogos from controller
        centros_contables: centros_contables

    },
    
    methods: {
        
        cambiarTipo: function (tipo)
        {
            if (_.isEmpty(tipo))
            {
                this.cotizacion_alquiler.empezar_desde_id = '';
            }
        },
        
        cambiarTipoId: function (tipo_id)
        {
            
            var context = this;
            
            context.cotizacion_alquiler.saldo = '';
            context.cotizacion_alquiler.credito = '';
            context.cotizacion_alquiler.centro_facturacion_id = '';
            context.cotizacion_alquiler.centros_facturacion = [];
            
            if (!_.isEmpty(tipo_id) && context.cotizacion_alquiler.empezar_desde_type == 'cliente')
            {
                var cliente = _.find(context.clientes, function(cliente){
                    return cliente.id == tipo_id;
                });
                context.cotizacion_alquiler.saldo = cliente.saldo_pendiente;
                context.cotizacion_alquiler.credito = cliente.credito_favor;
                context.cotizacion_alquiler.centros_facturacion = cliente.centro_facturable;
            }
            
        },
        
        guardar: function () {
            var context = this;
            var $form = $("#form_crear_cotizacion_alquiler");
            var tableErrors = $("#cotizacionesAlquilerItemsErros");
            
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
        }

    }

});

Vue.nextTick(function () {
    formularioCrearCotizacionAlquiler.guardar();
});