
Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));
Vue.directive('inputmask', require('./../../vue/directives/inputmask.vue'));
Vue.directive('pop_over_cantidad', require('./../../vue/directives/pop_over_cantidad.vue'));
Vue.directive('pop_over_precio', require('./../../vue/directives/pop_over_precio.vue'));
Vue.directive('item-comentario', require('./../../vue/directives/item-comentario.vue'));

var form_crear_traslado = new Vue({

    el: "#form_crear_traslado_div",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\Traslados\\Models\\Traslados2',
            comentable_id: '',

        },

        config: {

            vista: window.vista,
            select2:{width:'100%'},
            datepicker2:{dateFormat: "dd/mm/yy"},
            inputmask:{

                cantidad: {'mask':'9{1,4}','greedy':false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableArticulos:false,
            modulo:'traslados'

        },

        catalogos:{

            bodegas:window.bodegas,
            estados:window.estados,
            categorias:window.categorias,
            aux:{}

        },

        detalle:{
            id:'',
            fecha_creacion: moment().format('DD/MM/YYYY'),
            uuid_lugar_anterior: '',
            uuid_lugar:'',
            fecha_entrega:'',
            id_estado:'1',
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
            label:'Empezar traslado desde',
            type:'',
            types:[
                {id:'pedido',nombre:'Pedido'}
            ],
            id:'',
            pedidos:window.pedidos
        },

    },

    components:{

        'empezar_desde': require('./../../vue/components/empezar-desde.vue'),
        'detalle': require('./components/detalle.vue'),
        'articulos':require('./../../vue/components/tabla-dinamica.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    methods:{

      guardar: function () {
          var context = this;
          var $form = $("#form_crear_traslado");

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

    },

    ready:function(){

        var context = this;

        if(context.config.vista == 'editar'){

            Vue.nextTick(function(){

                context.empezable = $.extend(context.empezable, JSON.parse(JSON.stringify(window.empezable)));
                context.detalle = JSON.parse(JSON.stringify(window.traslado));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.traslado.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.traslado.id));

                context.config.disableEmpezarDesde = true;
                if(context.detalle.id_estado == '3')
                {
                    context.config.disableDetalle = true;
                    context.config.disableArticulos = true;
                }
                
            });

        }else{
            context.config.enableWatch = true;

            Vue.nextTick(function(){

                context.empezable.type = window.empezable.type;
                Vue.nextTick(function(){

                    context.empezable.id = window.empezable.id;

                });

            });
        }

    }
});

Vue.nextTick(function () {
    form_crear_traslado.guardar();
});
