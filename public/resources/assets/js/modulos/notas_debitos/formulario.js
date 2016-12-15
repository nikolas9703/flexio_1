

Vue.directive('select2', require('./../../vue/directives/select2.vue'));
Vue.directive('inputmask', require('./../../vue/directives/inputmask.vue'));
Vue.directive('datepicker', require('./../../vue/directives/datepicker.vue'));
Vue.filter('currencyDisplay', require('./../../vue/filters/currency-two-way.vue'));

Vue.http.options.emulateJSON = true;
var notaDebitoFormulario = new Vue({
  el:'#crear_nota_debito',
  data:{

      comentario: {

          comentarios: [],
          comentable_type: "Flexio\\Modulo\\NotaDebito\\Models\\NotaDebito",
          comentable_id: '',

      },

      catalogos:{

          proveedores: window.proveedores,
          centros_contables: window.centros_contables,
          usuarios: window.usuarios,
          estados: window.estados,
          cuentas: window.cuentas,
          impuestos: window.impuestos

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
        disableEmpezarDesde:false,
        disableDetalle:false,
        disableArticulos:false,
        modulo:'notas_debitos'

    },

    detalle:{

      proveedor_id:'',
      monto_factura:0,
      saldo_proveedor:0,
      credito_proveedor:0,
      fecha_factura:'',
      fecha:moment().format('DD/MM/YYYY'),
      creado_por:window.usuario_id,
      centro_contable_id:'',
      estado:'por_aprobar',
      no_nota_credito:'',
      //total, subtotal, impuesto
      filas:[
        {id:'', cuenta_id:'', monto:0, precio_total:0, descripcion: '', impuesto_total:0, impuesto_id:'', item_id:0}
      ]
    },

    empezable:{

      label:'Aplicar nota de d&eacute;bito a',
      type:'',
      types:[
        //al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
        {id:'factura',nombre:'Factura'},
      ],
      id:'',
      facturas:window.facturas
    },

    //se heredand e la estructura anterior
    tablaError:'',
    botonDisabled: false

  },

  components:{

    'empezar_desde': require('./../../vue/components/empezar-desde.vue'),
    'detalle': require('./components/detalle.vue'),
    'nota-debito-items': require('./components/nota-debito-items.vue'),
    'vista_comments': require('./../../vue/components/comentario.vue')

  },

  ready:function(){

    var context = this;

    if(context.config.vista === 'ver'){

        Vue.nextTick(function(){
            context.empezable = $.extend(context.empezable, JSON.parse(JSON.stringify(window.empezable)));
            context.detalle = JSON.parse(JSON.stringify(window.nota_debito));
            context.comentario.comentarios = JSON.parse(JSON.stringify(window.nota_debito.landing_comments));
            context.comentario.comentable_id = JSON.parse(JSON.stringify(window.nota_debito.id));

            context.config.disableEmpezarDesde = true;
            if(context.detalle.estado == 'aprobado' || context.detalle.estado == 'anulado')
            {
                context.config.disableDetalle = true;
            }

        });

    }

  },

  methods:{

      guardar: function () {
          var context = this;
          var $form = $("#form_crear_notaDebito");

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
                  $('input, select').prop('disabled', false);
                  $('form').find(':submit').prop('disabled',true);
                  form.submit();
              }
          });
      },

  }

});

Vue.nextTick(function () {
    notaDebitoFormulario.guardar();
});
