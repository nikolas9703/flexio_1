Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));

import calcular_movimientos from './mixins/calcular-movimientos';

var subcontratoFormulario = new Vue({

  el:'#form_crear_subcontrato_div',

  mixins: [calcular_movimientos],

  data:{

    comentario: {

      comentarios: [],
      comentable_type: "Flexio\\Modulo\\SubContratos\\Models\\SubContrato",
      comentable_id: '',

    },

    config: {

          vista: window.vista,
          select2:{width:'100%'},
          datepicker2:{dateFormat: "dd/mm/yy"},
          fecha_inicio:{
              dateFormat: 'dd/mm/yy',
              changeMonth: true,
              numberOfMonths: 1,
              onClose: function( selectedDate ) {
                  $("#fecha_final").datepicker( "option", "minDate", selectedDate );
              }
          },
          fecha_final:{
              dateFormat: 'dd/mm/yy',
              changeMonth: true,
              numberOfMonths: 1,
              onClose: function( selectedDate ) {
                  $("#fecha_inicio").datepicker( "option", "maxDate", selectedDate );
              }
          },
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
          agregar_adenda: typeof window.agregar_adenda !== 'undefined' ? true : false

      },

      catalogos:{

          proveedores: window.proveedores,
          centros_contables: window.centros_contables,
          estados: window.estados,
          cuentas : window.cuentas

      },

      detalle:{

          id: '',
          proveedor_id: '',
          fecha_inicio: '',
          fecha_final:'',
          centro_id:'',
          referencia:'',
          estado:'por_aprobar',
          monto_adenda:0,
          montos:[
              {cuenta_id:'', descripcion: '', monto:0}
          ],
          movimientos:[
              {monto:0, porcentaje: 0, cuenta_id:'', tipo:'abono'},
              {monto:0, porcentaje: 0, cuenta_id:'', tipo:'retenido'}
          ]

      }

    },

    components:{

        'detalle': require('./components/detalle.vue'),
        'montos': require('./components/montos.vue'),
        'movimientos': require('./components/movimientos.vue'),
        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_subcontrato");

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

        if(context.config.vista == 'ver' || context.config.vista == 'editar'){

            Vue.nextTick(function(){

                context.detalle = JSON.parse(JSON.stringify(window.subcontrato));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.subcontrato.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.subcontrato.id));

                if(context.detalle.estado == 'terminado' || context.detalle.estado == 'terminado')
                {
                  context.config.disableDetalle = true;
                }

            });

        }

    }

});

subcontratoFormulario.guardar();
