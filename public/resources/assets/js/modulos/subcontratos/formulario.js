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
           disablePermisoAdenda:window.permiso_adenda,
          agregar_adenda: typeof window.agregar_adenda !== 'undefined' ? true : false

      },

      catalogos:{

          proveedores: window.proveedores,
          cloneProveedores:[],//usado por el cambio de proveedores a ajax
          centros_contables: window.centros_contables,
          estados: window.estados,
          tipos_subcontratos: window.tipos_subcontratos,
          cuentas : window.cuentas

      },

      detalle:{

          id: '',
          proveedor_id: '',
          tipo_subcontrato_id: '',
          fecha_inicio: '',
          fecha_final:'',
          centro_id:'',
          referencia:'',
          estado:'por_aprobar',
          monto_adenda:0,
          montos:[
              {cuenta_id:'', descripcion: '', monto:''}
          ],
          movimientos:[
              {monto:0, porcentaje: 0, cuenta_id:'', tipo:'abono', label:'anticipo'},
              {monto:0, porcentaje: 0, cuenta_id:'', tipo:'retenido', label:'retenido'}
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
                   /* console.log($('.subcontrato_monto').val());
                    if($('.subcontrato_monto').val() === '0.00'){
                        $form.find('.tabla_dinamica_error').empty().append('<label class="error">El campo Monto (Sin ITBMS) es requerido, no puede ser 0.00</label>');
                    }*/
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

        selectProveedores(){
            var context = this;
            $("#proveedor_id").select2({
                width:'100%',
                ajax: {
                    url: phost() + 'proveedores/ajax_catalogo_proveedores',
                    dataType: 'json',
                    delay: 100,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            erptkn: tkn
                        };
                    },
                    processResults: function (data, params) {

                        let resultados = data.map(resp=> [{'id': resp.proveedor_id,'text': resp.nombre}]).reduce((a, b) => a.concat(b),[]);
                        context.catalogos.cloneProveedores = data;
                        console.log(resultados);
                        return {
                            results:resultados
                        }
                    },
                    escapeMarkup: function (markup) { return markup; },
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

                context.catalogos.proveedores = [window.subcontrato.proveedor];
                context.catalogos.cloneProveedores = _.clone(context.catalogos.proveedores);

                if(context.detalle.estado == 'terminado' || context.detalle.estado == 'terminado')
                {
                  context.config.disableDetalle = true;
                }
                  context.config.disablePermisoAdenda = window.permiso_adenda;
                  context.detalle.movimientos[0]['label'] ='anticipo';
                  context.detalle.movimientos[1]['label'] ='retenido';
            });
        }

        if(context.config.vista == 'crear'){
            this.selectProveedores();
            //set anticipo cuenta_id
            context.detalle.movimientos[0].cuenta_id = _.find(cuentas_contrato, function(o){return o.tipo == 'anticipo_activo'}).cuenta_id;
            context.detalle.movimientos[1].cuenta_id = _.find(cuentas_contrato, function(o){return o.tipo == 'retencion_pasivo'}).cuenta_id
        }

    }

});

subcontratoFormulario.guardar();
