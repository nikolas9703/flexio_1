
Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
var items = require('./../../config/lines_items.js');

var form_crear_ordenventa = new Vue({

    el: '#form_crear_ordenventa_div',

    data:{

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\OrdenesVentas\\Models\\OrdenVenta',
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
            modulo:'cotizaciones',//debe ir ordenes_ventas
            editarPrecio: window.editar_precio
        },
        catalogos:{

            clientes:window.clientes,
            terminos_pago:window.terminos_pago,
            vendedores:window.vendedores,
            precios:window.precios,
            centros_contables:window.centros_contables,
            bodegas:window.bodegas,
            estados:window.estados,
            categorias:window.categorias,
            cuentas:window.cuentas,
            impuestos:window.impuestos,
            usuario_id:window.usuario_id,
            aux:{}
        },

        detalle:{

            id:'',
            termino_pago:'al_contado',
            saldo_cliente:0,
            credito_cliente:0,
            fecha_desde:moment().format('DD/MM/YYYY'),
            fecha_hasta:moment().add(30,'days').format('DD/MM/YYYY'),
            cliente_id:'',
            creado_por:'',
            item_precio_id:'',
            centro_contable_id:'',
            centro_facturacion_id:'',
            centros_facturacion:[],
            bodega_id:'',
            estado:'abierta',
            observaciones:'',
            articulos:[
                {
                    id:'',
                    cantidad: '',
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
            label:'Empezar orden de venta desde',
            type:'',
            types:[
                {id:'cotizacion',nombre:'Cotizaci&oacute;n'}
            ],
            id:'',
            cotizacions:window.cotizaciones
        }

    },

    components:{
        'articulos':require('./../../vue/components/tabla-dinamica.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue'),
        'totales':require('./../../vue/components/tabla-totales.vue')
    },

    ready:function(){

        var context = this;

        if(context.config.vista == 'editar'){

            context.config.disableEmpezarDesde = true;
            context.empezable = $.extend({label:context.empezable.label,types:context.empezable.types},window.empezable);
            Vue.nextTick(function(){

                context.detalle = JSON.parse(JSON.stringify(window.orden_venta));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.orden_venta.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.orden_venta.id));

                if(context.detalle.estado == 'abierta'){
                    context.catalogos.estados.splice(2,2);
                }

                if(context.detalle.estado == 'por_facturar'){
                    context.catalogos.estados.splice(0,1);
                    context.catalogos.estados.splice(1,2);
                }

                if(context.detalle.estado == 'aprobada' || context.detalle.estado == 'facturado_parcial' || context.detalle.estado == 'facturado_completo' || context.detalle.estado == 'anulada'){

                    context.config.disableDetalle = true;
                    context.config.disableArticulos = true;

                }
                Vue.nextTick(function(){

                    context.config.enableWatch = true;

                });

            });

        }else{

            Vue.nextTick(function(){

                context.config.enableWatch = true;
                if(context.config.vista == 'crear'){

                    context.empezable.type = window.empezable.type;
                    Vue.nextTick(function(){

                        context.empezable.id = window.empezable.id;

                    });

                }

            });

        }



    },
    computed:{
        disableBodega:function(){

            var tipos = _.map(this.detalle.articulos,'tipo_id');
            if(_.includes(tipos,4)){
                return true;
            }else if (_.includes(tipos,5)) {
                return true;
            }
            return false;
        }
    },
    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_ordenventa");

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
                    form.submit();
                }
            });
        }

    }

});
//el campo de bodega debe estar habilitado para la creaciond de orden de venta y la edicion --Jaime Chung verificado por Roberto Boyd 1-nov-2016
/*form_crear_ordenventa.$watch("detalle.articulos",function(row, old){
    var tipos = _.map(form_crear_ordenventa.detalle.articulos,'tipo_id');
    if(_.includes(tipos,4) || _.includes(tipos,5)){
        $("#bodega_id").prop('disabled',false);
        form_crear_ordenventa.disableBodega = false;
    }else{
        $("#bodega_id").prop('disabled',true);
        form_crear_ordenventa.disableBodega = true;
    }
},{deep: true});*/

Vue.nextTick(function () {
    form_crear_ordenventa.guardar();
});
