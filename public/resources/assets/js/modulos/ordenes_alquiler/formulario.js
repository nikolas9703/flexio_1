
Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
var form_crear_ordenventa = new Vue({

    el: '#form_crear_ordenventa_div',

    data:{

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\OrdenesAlquiler\\Models\\OrdenVentaAlquiler',
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
            disableValidate: true, //no validar campos
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableArticulos:false,
            modulo:'ordenes_alquiler',//debe ir ordenes_ventas
            editarPrecio: window.editar_precio,
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
            precios_alquiler: window.lista_precio_alquiler,
            aux:{}
        },

        detalle:{
            id:'',
            termino_pago:'al_contado',
            saldo_cliente:0,
            credito_cliente:0,
            fecha_desde:moment().format('DD/MM/YYYY'),
            fecha_hasta:moment().add(30,'days').format('DD/MM/YYYY'),
            creado_por:'',
            item_precio_id:'',
            precio_alquiler_id: '',
            centro_contable_id:'',
            centro_facturacion_id:'',
            centros_facturacion:[],
            precio_alquiler_id:'',
            bodega_id:'',
            estado:'abierta',
            observaciones:'',
            articulos_alquiler_loader: '',
            articulos_alquiler:[
                /*{
                    item_id: '',
                    cantidad: '',
                    precio_total: '',
                    tarifa_pactada: '',
                    tarifa_fecha_desde: '',
                    tarifa_fecha_hasta: '',
                    tarifa_periodo_id: '',
                    tarifa_monto: '',
                    tarifa_cantidad_periodo: '',
                    precio_unidad: '', //precio unidad = tarifa pactada
                    precio_total: '',
                    item: ''
                }*/
            ],
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
            ],
            cargos_adicionales_checked: false
        },
        empezable:{
            label:'Empezar orden de venta desde',
            type:'',
            types:[
                {id:'contrato',nombre:'Contrato de alquiler'}
            ],
            id:'',
            contratos: window.contratos
        }
    },

    components:{
        'articulos':require('./../../vue/components/tabla-dinamica.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue'),
        'cargos_alquiler':require('./../../vue/components/tabla-cargos-alquiler.vue'),
        'totales':require('./../../vue/components/tabla-totales.vue')
    },

    ready:function(){

        var scope = this;
      	var togglecargoadicional = document.querySelector('#cargos_adicionales');
      	var switchery = new Switchery(togglecargoadicional, {color:"#1ab394", size: 'small'});

        // mostrar ocultar cargos adicionales
        // plugin: switchery
        togglecargoadicional.onchange = function() {
            var checked = this.checked;
            Vue.nextTick(function(){

            //reset detalle articulos
            scope.detalle.cargos_adicionales_checked = checked;
            checked == false ? scope.detalle.articulos = [{
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
              }] : '';
            });
        };

        if(scope.config.vista == 'editar'){

            scope.config.disableEmpezarDesde = true;
            scope.empezable = $.extend({label:scope.empezable.label,types:scope.empezable.types},window.empezable);
            Vue.nextTick(function(){

                scope.detalle = JSON.parse(JSON.stringify(window.orden_venta));
                //scope.detalle.articulos = typeof scope.detalle.articulos != 'undefined' && scope.detalle.articulos.length > 0 ? scope.detalle.articulos.items : [];
                scope.comentario.comentarios = JSON.parse(JSON.stringify(window.orden_venta.comentario_timeline));
                scope.comentario.comentable_id = JSON.parse(JSON.stringify(window.orden_venta.id));

                if(scope.detalle.articulos.length > 0){
                  scope.detalle.cargos_adicionales_checked = true;
                  switchery.bindClick();
                }else{
                  scope.detalle.articulos = [{
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
                  }];
                }

                if(scope.detalle.estado == 'abierta'){
                    scope.catalogos.estados.splice(2,2);
                }

                if(scope.detalle.estado == 'por_facturar'){
                    scope.catalogos.estados.splice(0,1);
                    scope.catalogos.estados.splice(1,2);
                }

                if(scope.detalle.estado == 'facturado_parcial' || scope.detalle.estado == 'facturado_completo' || scope.detalle.estado == 'anulada'){

                    scope.config.disableDetalle = true;
                    scope.config.disableArticulos = true;

                }
                Vue.nextTick(function(){
                    scope.config.enableWatch = true;
                });

            });

        }else{
            Vue.nextTick(function(){

                scope.config.enableWatch = true;
                if(scope.config.vista == 'crear'){

                    scope.empezable.type = window.empezable.type;
                    Vue.nextTick(function(){

                        scope.empezable.id = window.empezable.id;

                    });

                }

            });
        }
    },

    methods:{

        guardar: function () {
            var scope = this;
            var $form = $("#form_crear_ordenventa");

            $form.validate({
              //debug: true,
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

                    $('input:disabled, select:disabled').removeAttr('disabled');
                    $('form').find('#submit').prop('disabled',true);
                    form.submit();
                }
            });
        }

    }

});

Vue.nextTick(function () {
    form_crear_ordenventa.guardar();
});
