Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});

var form_crear_cotizacion = new Vue({

    el: '#form_crear_cotizacion_div',

    data:{

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\Cotizaciones\\Models\\Cotizacion',
            comentable_id: '',

          },

        config:{

            vista:window.vista,
            enableWatch:false,
            select2:{width:'100%'},
            datepicker2:{dateFormat: "dd/mm/yy"},
            fecha_desde:{
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
                }
            },
            fecha_hasta:{
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
                }
            },
            inputmask:{

                cantidad: {'mask':'9{1,4}','greedy':false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableArticulos:false,
            modulo:'cotizaciones',
            editarPrecio: window.editar_precio

        },

        catalogos:{

            terminos_pago:window.terminos_pago,
            vendedores:window.vendedores,
            precios:window.precios,
            centros_contables:window.centros_contables,
            estados:window.estados,
            categorias:window.categorias,
            cuentas:window.cuentas,
            impuestos:window.impuestos,
            usuario_id:window.usuario_id,
            aux:{}

        },

        detalle:{
            
            id:'',
            cliente_id:empezable.id,
            termino_pago:'al_contado',
            saldo_cliente:0,
            credito_cliente:0,
            fecha_desde:moment().format('DD/MM/YYYY'),
            fecha_hasta:moment().add(30,'days').format('DD/MM/YYYY'),
            creado_por:'',
            item_precio_id:'',
            centro_contable_id:'',
            centro_facturacion_id:'',
            centros_facturacion:[],
            estado:'por_aprobar',
            observaciones:'',
            oportunidad_id:'',
            articulos:[
                {
                    id:'',
                    cantidad: 1,
                    categoria_id: '',
                    cuenta_id: '',
                    cuentas:'[]',
                    descuento: '',
                    impuesto_id: '',
                    item_id: '',
                    item_hidden_id: '',
                    items:[],
                    precio_total: '',
                    precio_unidad: '',
                    precios:[],
                    unidad_id: '',
                    unidad_hidden_id:'',
                    unidades:[],
                    descripcion: '',
                    facturado:false,
                    atributos:[],
                    atributo_text:'',
                    atributo_id:''
                }
            ]

        },

        empezable:{
            label:'Empezar cotizaci&oacute;n desde',
            type:'',
            types:[
                {id:'cliente',nombre:'Cliente'},
                {id:'cliente_potencial',nombre:'Cliente potencial'}//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
            ],
            id:'',
            clientes:window.clientes,
            cliente_potencials:window.cliente_potencials
        }

    },

    components:{
        'articulos':require('./../../vue/components/tabla-dinamica.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready:function(){

        var context = this;

        if(context.config.vista == 'editar'){


            context.config.disableEmpezarDesde = true;
            context.empezable = $.extend({label:context.empezable.label,types:context.empezable.types},window.empezable);
            Vue.nextTick(function(){

                context.detalle = JSON.parse(JSON.stringify(window.cotizacion));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.cotizacion.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.cotizacion.id));

                if(context.detalle.estado == 'por_aprobar'){
                    context.catalogos.estados.splice(2,2);
                }

                if(context.detalle.estado == 'aprobado'){
                    context.catalogos.estados.splice(0,1);
                    context.catalogos.estados.splice(1,1);
                }

                if(context.detalle.estado == 'ganado' || context.detalle.estado == 'perdido' || context.detalle.estado == 'anulado'){

                    context.config.disableDetalle = true;
                    context.config.disableArticulos = true;

                }
                Vue.nextTick(function(){

                    context.config.enableWatch = true;

                });

            });

            //context.catalogos.aux = JSON.parse(JSON.stringify(window.cotizacion));

        }else{

            Vue.nextTick(function(){

                context.config.enableWatch = true;
                console.log('active watch');
                if(context.config.vista == 'crear'){

                    context.empezable.type = window.empezable.type;
                    Vue.nextTick(function(){

                        context.empezable.id = window.empezable.id;

                    });

                }

            });

        }



    },

    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_cotizacion");

            $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    var self = $(element);
                    if (self.closest('div').hasClass('input-group') && !self.closest('table').hasClass('itemsTable')) {
                        element.parent().parent().append(error);
                    }else if(self.closest('div').hasClass('form-group') && !self.closest('table').hasClass('itemsTable')){
                        self.closest('div').append(error);
                    }else if(self.closest('table').hasClass('itemsTable')){
                        $form.find('.tabla_dinamica_error').empty().append('<label class="error">Estos campos son obligatorios (*).</label>');
                    }else{
                        error.insertAfter(error);
                    }
                },
                submitHandler: function (form) {
                    //context.disabledHeader = false;
                    //context.disabledEstado = false;
                    $('input, select').prop('disabled', false);
                    $('form').find(':submit').prop('disabled',true);
                    form.submit();;
                }
            });
        }

    }

});

Vue.nextTick(function () {
    form_crear_cotizacion.guardar();
});
