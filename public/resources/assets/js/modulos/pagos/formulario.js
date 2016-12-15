
Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));

var form_crear_pago = new Vue({

    el: "#form_crear_pago_div",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\Pagos\\Models\\Pagos",
            comentable_id: '',

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
            disableDetalle:false

        },

        catalogos:{

            proveedoresList:window.proveedoresList,
            proveedores:window.proveedores,
            cuentas:window.cuentas,
            cajas:window.cajas,
            metodos_pago:window.metodos_pago,
            bancos:window.bancos,
            estados:window.estados,
            tipos_pago:window.tipos_pago,
            aux:{}

        },

        detalle:{
            id:'',
            codigo: '',
            fecha_pago: moment().format('DD/MM/YYYY'),
            proveedor_id: '',
            monto_pagado: '',
            estado: 'por_aprobar',
            formulario: '',
            depositable_type:'',
            depositable_id:'',
            saldo_proveedor:0,//components/detalle
            credito_proveedor:0,//components/detalle
            pagables:[],
            metodos_pago:[
                {tipo_pago:'', total_pagado:0, referencia:{
                  nombre_banco_ach: '', cuenta_proveedor: '', numero_cheque:'', nombre_banco_cheque:'', numero_tarjeta:'', numero_recibo:''
                }}
            ]

        },

        empezable:{
            label:'Aplicar pago a',
            type:'',
            types:[
                //al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
                {id:'factura',nombre:'Factura'},
                {id:'proveedor',nombre:'Proveedor'},
                {id:'subcontrato',nombre:'Subcontrato'}
            ],
            id:'',
            facturas:window.facturas,
            proveedors:window.proveedores,
            subcontratos:window.subcontratos
        },

    },

    components:{

        'empezar_desde': require('./../../vue/components/empezar-desde.vue'),
        'detalle': require('./components/detalle.vue'),
        'pagables': require('./components/pagables.vue'),
        'monto': require('./components/monto.vue'),
        'metodos_pago': require('./components/metodos_pago.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    computed:{

        sePuedeGuardar: function (){

            var context = this;
            var monto = _.sumBy(context.detalle.pagables, function(pagable){
              return parseFloat(pagable.monto_pagado) || 0;
            });

            var pagado = _.sumBy(context.detalle.metodos_pago, function(metodo_pago){
              return parseFloat(metodo_pago.total_pagado) || 0;
            });

            if(monto != pagado || (pagado === 0))
            {
                return false;
            }
            return true;

        }

    },

    methods:{

      guardar: function () {
          var context = this;
          var $form = $("#form_crear_pago");

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
                context.detalle = JSON.parse(JSON.stringify(window.pago));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.pago.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.pago.id));

                context.config.disableEmpezarDesde = true;
                context.config.disableDetalle = true;
 
                if(politica_transaccion.length === 0){
                  console.log("que tal todos blas");
                    toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");
                    setTimeout(function(){
                    $('.estado').attr('disabled', true);
                    $('.btn-primary').attr('disabled', true);
                  }, 400);
                    return true;
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

    },
});

Vue.nextTick(function () {
    form_crear_pago.guardar();
});
