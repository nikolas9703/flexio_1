





Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.directive('inputmask', require('./../../vue/directives/inputmask.vue'));
Vue.directive('pop_over_cantidad', require('./../../vue/directives/pop_over_cantidad.vue'));
Vue.directive('pop_over_precio', require('./../../vue/directives/pop_over_precio.vue'));

var ajustes_form_app = new Vue({

    el: '#ajustes_form_div',

    data: {

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\Ajustes\\Models\\Ajustes2',
            comentable_id: '',

        },

        config:{

            vista: window.vista,
            enableWatch: false,
            select2: {width:'100%'},
            datepicker2: {dateFormat: "dd/mm/yy"},
            inputmask:{

                cantidad: {'mask':'9{1,4}','greedy':false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableDetalle:false,
            disableArticulos:false,
            modulo:'ajustes'

        },

        catalogos:{

            centros_contables: window.centros_contables,
            bodegas: window.bodegas,
            tipos_ajustes: window.tipos_ajustes,
            estados: window.estados,
            categorias: window.categorias,
            cuentas: window.cuentas,
            razones : window.razones,
            //empresa:window.empresa,
            aux:{}

        },

        detalle:{

            id:'',
            centro_id:'',
            uuid_bodega:'',
            tipo_ajuste_id:'',
            estado_id:'3',//por aprobar
            created_at:moment().format('DD/MM/YYYY'),
            descripcion:'',
            razon_id:'',
            created_by:'',
            comentarios:'',
            total:0,
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
                    unidad_id: '',
                    unidad_hidden_id:'',
                    unidades:[],
                    descripcion: '',
                    facturado:false,
                    atributos:[],
                    atributo_text:'',
                    atributo_id:'',
                    seriales:[],
                    tipo_id:''
                    //falta saber si es serializado o no para mostrar los input para los seriales
                }
            ]

        }

    },

    components:{

        'detalle': require('./components/detalle.vue'),
        'articulos': require('./../../vue/components/tabla-dinamica.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready:function(){

        var context = this;

        if(context.config.vista == 'editar'){

            Vue.nextTick(function(){

                context.detalle = JSON.parse(JSON.stringify(window.ajuste));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.ajuste.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.ajuste.id));
                context.catalogos.aux = JSON.parse(JSON.stringify(window.ajuste));

                if(context.detalle.estado_id > 3){
                    context.config.disableDetalle = true;
                    context.config.disableArticulos = true;
                    context.catalogos.estados.splice(0,1);
                }

                Vue.nextTick(function(){
                    context.config.enableWatch = true;
                });

            });

        }else{

            context.config.enableWatch = true;

        }



    },

    computed:{

        getItemNoValido:function(){

            var context = this;
            var items_no_validos = _.sumBy(context.detalle.articulos, function(articulo){
                return (articulo.item_id !== '' && articulo.precio_unidad == '0.0000') ? 1 : 0;
            });
            if(items_no_validos > 0){
                //toastr['warning']('Un ajuste no debe poseer items con precio de unidad igual a cero (0)');
                return true;
            }
            return false;

        }

    },

    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#ajustes_form");

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
                    $('input, select').prop('disabled', false);
                    $('form').find(':submit').prop('disabled',true);
                    form.submit();
                }
            });
        }

    }

});

Vue.nextTick(function () {
    ajustes_form_app.guardar();
});
