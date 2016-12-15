Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});

import Articulos from '../../../js/items';

var formularioCrearContratoAlquiler = new Vue({

    el: '#appContratoAlquiler',

    data: {

        //custom
        mostrar:{
            icono_pop_over:false
        },

        config: {
                modulo:'contratos_alquiler',
                select2:{width:'100%'},
                vista: window.vista,
                enableWatch:false,
                disableEmpezarDesde:(window.disableEmpezarDesde==0)?false:true,
                disabledClienteId: true,
        },

        catalogos:{

            cuentas:window.cuentas,
            periodos_tarifario: _.orderBy(window.ciclos_tarifarios, ['orden'], ['asc']),
            impuestos:window.impuestos,
            categorias:window.categorias

        },

        detalle:{enableWatch: true},
        empezable:{
            label:'Empezar contrato desde',
            type:'',
            /*types:[
                {id:'cliente',nombre:'Cliente'},//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
                {id:'cotizacion',nombre:'Cotizacion'}//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
            ],*/
            types:window.empezable.types,
            id:'',
            clientes:window.clientes,
            cotizacions:window.cotizacions,
        },
        vista: vista,
        disabledHeader: false,
        disabledEstado: true,
        disabledEditar: false,
        clientes: clientes, //catalogos from controller
        vendedores: vendedores, //catalogos from controller
        estados: estados, //catalogos from controller
        cortes_facturacion: cortes_facturacion, //catalogos from controller
        costos_retorno: costos_retorno, //catalogos from controller
        centros_contables: centros_contables, //catalogos from controller
        dia_corte: dia_corte, //catalogos from controller
        preguntas_cerrada: preguntas_cerrada, //catalogos from controller
        lista_precio_alquiler: lista_precio_alquiler,
        contrato_alquiler: {
            id:'',
            codigo: codigo,
            centros_facturacion: [],
            centro_facturacion_id: '',
            corte_facturacion_id:'',
            calculo_costo_retorno_id:'',
            facturar_contra_entrega_id: pregunta_cerrada_default,
            lista_precio_alquiler_id: '',
            saldo: '',
            credito: '',
            vendedor_id: '',
            centro_contable_id: '',
            dia_corte: '',
            estado_id: '1',//por aprobar
            observaciones:'',
            articulos:Articulos.items
        },

    },

    components:{

        'articulo-agrupador': require('./../../vue/components/articulo-agrupador.vue')

    },

    ready: function ()
    {
        var context = this;
        if(context.vista == 'editar')
        {

            Vue.nextTick(function(){
                   //context.empezable = $.extend({label:context.empezable.label,types:context.empezable.types},window.empezable);
                   context.contrato_alquiler = contrato_alquiler;
                   //mutable
                   Articulos.items = context.contrato_alquiler.articulos;

                   context.disabledEstado = false;

                   if(context.contrato_alquiler.estado_id > '2')//anulado o terminado
                   {
                       context.disabledEditar = true;
                   }
            });
          }


     Vue.nextTick(function(){

                context.config.enableWatch = true;

                 if(context.vista == 'crear'){


                    context.empezable.type = window.empezable.type;
                    Vue.nextTick(function(){
                         context.empezable.id = window.empezable.id;
                     });




                }
             });


    },


     computed:{

        disabledCorteFacturacion:function(){
            //this.$set('contrato_alquiler.dia_corte','');

            if(_.includes([11,20],this.contrato_alquiler.corte_facturacion_id)){
                return false;
            }

            return true;
        },

    },
    methods: {
        cambioDeCorteFacturacion:function(tipo){

            this.$set('contrato_alquiler.dia_corte' , '');
        },
        guardar: function () {
            var context = this;
            var $form = $("#form_crear_contrato_alquiler");
            var tableErrors = $("#contratosAlquilerItemsErros");

            $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                	var self = $(element);
                    tableErrors.html(' ');
                    if (self.closest('table').length > 0) {
                        tableErrors.html('<label class"error" style="color:red;">Estos campos son obligatorios</label>');
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    context.disabledHeader = false;
                    context.disabledEstado = false;
                    $('input, select').prop('disabled', false);
                    Vue.nextTick(function () {
                        form.submit();
                    });
                }
            });
        }

    },
     watch: {                       //5,2
        'empezable.id': function (val, oldVal) {
             var context = this;

            if( context.empezable.type == 'cotizacion' && val!=''){
                var comenzable_id = context.empezable.id;
                var cotizacion = _.find(context.empezable.cotizacions, function(cotizacion){
                    return cotizacion.id == comenzable_id;
                });
                context.contrato_alquiler.observaciones = cotizacion.observaciones;

                context.$broadcast('popular_articulos', cotizacion.articulos );//contratos_items*/
            }
        },
        'empezable.type': function (val, oldVal) {
               var context = this;
              if(oldVal!='')
               context.$broadcast('limpiando_articulos');
        },
        'contrato_alquiler.lista_precio_alquiler_id': function (val, oldVal) {

          if(_.isEmpty(val) && this.vista != 'editar'){
            //Si no selecciona ninguna lista de precio
            //limpiar valores de periodo y tarifa.
            _.forEach(this.contrato_alquiler.articulos, function(articulo) {
              Vue.nextTick(function(){
                articulo.periodo_tarifario = '';
                articulo.tarifa = '';
              });
            });
            return;
          }

          //Al cambiar precio de lista
          //actualizar las tarifas.
          this.$broadcast('setTarifa');
        }
     },



});

Vue.nextTick(function () {
    formularioCrearContratoAlquiler.guardar();
});
