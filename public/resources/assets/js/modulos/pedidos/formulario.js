Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});

var form_crear_pedido = new Vue({

    el: '#form_crear_pedido_div',

    data: {

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\Pedidos\\Models\\Pedidos',
            comentable_id: '',

          },

        config: {

            vista: window.vista,
            politicaTransaccion:window.politica_transaccion,
            enableWatch: false,
            select2: { width: '100%' },
            datepicker2: { dateFormat: 'dd/mm/yy' },
            inputmask: {

                cantidad: { mask: '9{1,4}', greedy: false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableArticulos:false,
            modulo:'pedidos',//debe ir ordenes_ventas
            disableFechaCreacion: true

        },

        catalogos:{

            centros_contables:window.centros_contables,
            bodegas:window.bodegas,
            compradores:window.compradores,
            estados:window.estados,
            categorias:window.categorias,
            cuentas:window.cuentas,
            impuestos:window.impuestos,
            usuario_id:window.usuario_id,
            unidades:window.unidades,//quick add component
            tipos_item:window.tipos_item,//quick add component
            aux:{}

        },

        detalle:{

            id:'',
            fecha_creacion:moment().format('DD/MM/YYYY'),
            uuid_centro:'',
            uuid_lugar:'',
            creado_por:window.usuario_id,
            referencia:'',
            estado:'1',//por aprobar
            observaciones:'',
            articulos:[
                {
                    id:'',
                    cantidad: '',
                    categoria_id: '',
                    cuenta_id: '',
                    item_id: '',
                    item_hidden_id: '',
                    items:[],
                    unidad_id: '',
                    unidad_hidden_id:'',
                    unidades:[],
                    descripcion: '',
                    facturado:false,
                    atributos:[],
                    atributo_text:'',
                    atributo_id:'',
                    cuentas:'[]'
                }
            ]

        },
        disabledPorPolitica: false

    },

    components:{
        'articulos':require('./../../vue/components/tabla-dinamica.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },
    ready:function(){

        var context = this;
        if(context.config.vista == 'editar'){


            context.config.disableEmpezarDesde = true;
            Vue.nextTick(function(){

                context.detalle = JSON.parse(JSON.stringify(window.pedido));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.pedido.comentario));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.pedido.id));

                if(context.detalle.estado == '1'){//por aprobar
                    context.catalogos.estados.splice(2,2);
                }

                if(context.detalle.estado == '2'){
                    context.catalogos.estados.splice(0,1);
                    context.catalogos.estados.splice(1,2);
                }

                if(context.detalle.estado > '2'){

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

            });

        }



    },
    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_pedido");

            $form.validate({
                //debug:true,
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
        },
        inPolitica: function(){

            var estado = this.detalle.estado;
            var self = this;
            var politicas = this.config.politicaTransaccion;

            if(self.config.vista !== "editar"){
                return false;
            }

            if(politicas.length === 0){
                toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");              
                return false;
            }

            //filtros respectiva categoria y montos ejemplo [{categoria_id: 1, monto_limite:"200.00"}]
            var modulo_politicas =_.flattenDeep(_.map(politicas,function(policy){

                    return _.map(policy.categorias,function(pol){
                        return {categoria_id:pol.id};

                     });
                }));

                //elimino los duplicados y obtengo los montos mayores;
                var politica_categoria = _.uniqBy(modulo_politicas, 'categoria_id');

                ///buscar y filtrar si la politica esta los articulos
                ///cuando se cumpla enviar mensaje y bloquer boton

               var colecion_articulos = this.detalle.articulos;

               var categoriaArticuloPolitica = _.map(_.filter(colecion_articulos,function(articulo){
                   return  !_.some(politica_categoria, { 'categoria_id': parseInt(articulo.categoria_id) });
               }),function(a){ return {categoria_id:parseInt(a.categoria_id)};});

                if(categoriaArticuloPolitica.length === 0){
                    return false;
                }

                var objfiltradoCat = _.uniqBy(categoriaArticuloPolitica, 'categoria_id');

                var politica_aplicada = _.filter(objfiltradoCat,function(value,key){
                    return  politica_categoria[key].categoria_id !== parseFloat(value.categoria_id);
                });

                if(politica_aplicada.length > 0){
                    _.forEach(politica_aplicada,function(mensaje){
                        var categoria = _.find(self.catalogos.categorias,['id', mensaje.categoria_id]);
                        toastr["info"]("No cuenta con los permiso para cambiar el estado", "Categor\u00EDa "+categoria.nombre);
                        toastr.options = {
                           "closeButton": true,
                           "preventDuplicates": true,
                           "showDuration": "300",
                           "hideDuration": "0",
                           "timeOut": "7000",
                           "extendedTimeOut": "1000",
                         };
                    });

                    return true;
                }


                return false;

        }

    },
    watch:{
        'detalle.estado': function (val, oldVal) {
             var context = this;
             if(context.config.vista != 'crear' && context.config.politicaTransaccion.length > 0){
                 var politica = _.head(context.config.politicaTransaccion);
                 var estadoPolitica = politica.estado_politica.estado2;
                 if(val == politica.estado_politica.estado2){
                     context.disabledPorPolitica = context.inPolitica();
                 }else{
                     context.disabledPorPolitica = false;
                 }

              }
        }
    }

});

Vue.nextTick(function () {
    form_crear_pedido.guardar();
});
