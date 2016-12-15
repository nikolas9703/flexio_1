Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});

var items = require('./../../config/lines_items.js'); //objecto compartido del items
var form_factura_compra = new Vue({

    el: '#form_crear_facturas_div',

    data: {

        comentario: {

            comentarios: [],
            comentable_type: 'Flexio\\Modulo\\FacturasCompras\\Models\\FacturaCompra',
            comentable_id: '',

          },

        config:{

            vista:window.vista,
            politicaTransaccion:window.politica_transaccion,
            enableWatch:false,
            select2:{width:'100%'},
            datepicker2:{dateFormat: "dd/mm/yy"},
            inputmask:{

                cantidad: {'mask':'9{1,4}','greedy':false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            //disabledPorCantidad:false,
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableArticulos:false,
            facturaSuspendida:false,
            modulo:'facturas_compras'

        },

        catalogos:{

            proveedores:window.proveedores,
            terminos_pago:window.terminos_pago,
            usuarios:window.usuarios,
            centros_contables:window.centros_contables,
            bodegas:window.bodegas,
            estados:window.estados,
            categorias:window.categorias,
            cuentas:window.cuentas,
            impuestos:window.impuestos,
            empresa:window.empresa,
            aux:{}

        },

        detalle:{

            codigo: typeof window.codigo !== 'undefined' ? window.codigo : '', //requerido -> empezar_desde.js
            id:'',
            proveedor_id:'',
            terminos_pago:'',
            //saldo pendiente
            //credito a favor
            nro_factura_proveedor:'',
            fecha:moment().format('DD/MM/YYYY'), //requerido -> empezar_desde.js
            creado_por:window.usuario_id,
            centro_contable_id:'',
            recibir_en_id:'',
            estado:'13',//requerido -> empezar_desde.js
            observaciones:'',
            pagos:0,
            saldo:0,
            saldo_proveedor:0,
            credito_proveedor:0,
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
                    cantidad_maxima:''
                }
            ]

        },

        empezable:{
            label:'Empezar factura desde',
            type:'',
            types:[
                //al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
                {id:'orden_compra',nombre:'&Oacute;rdenes de compra'},
                {id:'subcontrato',nombre:'Subcontrato'}
            ],
            id:'',
            orden_compras:window.orden_compras,
            subcontratos:window.subcontratos
        },
        disabledPorPolitica: false,
        desHabilitandoPorCantidad:false
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

                context.empezable = $.extend({label:context.empezable.label,types:context.empezable.types},JSON.parse(JSON.stringify(window.empezable)));
                context.detalle = JSON.parse(JSON.stringify(window.factura));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.factura.comentario));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.factura.id));
                context.catalogos.aux = JSON.parse(JSON.stringify(window.factura));


                if(context.detalle.estado == 13){
                    context.catalogos.estados.splice(2,2);
                }

                if(context.detalle.estado == 14){
                    context.catalogos.estados.splice(0,1);
                    context.catalogos.estados.splice(1,2);
                }

                if(context.detalle.estado == 20){
                    context.catalogos.estados.splice(1,3);
                   // context.config.facturaSuspendida = true;
                }

                if(context.detalle.estado > 14 && context.detalle.estado !=20){ //sE AGREGP EL 20 POR QUE AHORA CUANDO ESTA SUSPEDIDO SE PUEDE EDITAR

                    context.config.disableDetalle = true;
                    context.config.disableArticulos = true;

                }
                Vue.nextTick(function(){
                    context.config.enableWatch = true;
                });

            });

        }else{

            context.config.enableWatch = true;

            Vue.nextTick(function(){

                if(context.config.vista == 'crear'){

                    context.empezable.type = window.empezable.type;
                    Vue.nextTick(function(){

                        context.empezable.id = window.empezable.id;

                    });

                }

            });

        }

        Vue.nextTick(function(){

        if(context.config.disableDetalle == true){
              toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");

          }

        });

    },
    methods:{

        guardar: function () {
            var context = this;
            var $form = $("#form_crear_facturas");

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
        },

        inPolitica: function(politicasQueCumplen){


            var estado = this.detalle.estado;
            var self = this;
            var politicas = this.config.politicaTransaccion;

            if(self.config.vista !== "editar"){

                return false;
            }
            console.log(politicas.length);
            if(politicas.length === 0){
                toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");
                return false;
            }

            //filtros respectiva categoria y montos ejemplo [{categoria_id: 1, monto_limite:"200.00"}]
            var modulo_politicas =_.flattenDeep(_.map(politicasQueCumplen,function(policy){

                    return _.map(policy.categorias,function(pol){
                        return {categoria_id:pol.id,monto_limite:policy.monto_limite};

                     });
                   }));

            //elimino los duplicados y obtengo los montos mayores;

            var politica_categoria = _.uniqWith(modulo_politicas,function(a, b){

                if(a.categoria_id === b.categoria_id){
                   if(a.monto_limite > b.monto_limite){
                       return true;
                   }else{
                       return true;
                   }
              }
            });

            ///buscar y filtrar si la politica esta los articulos
            ///cuando se cumpla enviar mensaje y bloquer boton

           var colecion_articulos = this.detalle.articulos;

           var categoriaArticuloPolitica = _.map(_.filter(colecion_articulos,function(articulo){
               return  _.some(politica_categoria, { 'categoria_id': parseInt(articulo.categoria_id) });
           }),function(a){ return {categoria_id:parseInt(a.categoria_id),subtotal: a.subtotal};});

            if(categoriaArticuloPolitica.length === 0){
                return false;
            }
            ///console.log(politica_categoria,categoriaArticuloPolitica);
            ///creo un array para comparar
            var objfiltradoCat = _.uniqWith(categoriaArticuloPolitica,function(a, b){

                if(a.categoria_id === b.categoria_id){
                  b.subtotal = parseFloat(a.subtotal) + parseFloat(b.subtotal);
                  return true;
                }

            });

            //var objfiltradoCat =

            var politica_aplicadas = _.filter(politica_categoria,function(value){
                return _.some(objfiltradoCat, { 'categoria_id': value.categoria_id });
            });

            var politica_aplicada = _.filter(politica_aplicadas,function(value,key){
                return  objfiltradoCat[key].subtotal > parseFloat(value.monto_limite);
            });


            if(politica_aplicada.length > 0){
                _.forEach(politica_aplicada,function(mensaje){
                    var categoria = _.find(self.catalogos.categorias,['id', mensaje.categoria_id]);
                    toastr["info"]("El monto limite para su aprobaci\u00F3n es " + mensaje.monto_limite, "Categor\u00EDa "+categoria.nombre);
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

            // context.config.disableBotonForEstado = true; //Se desbloquea x el momento el boton
             /*if(context.config.vista != 'crear' && context.config.politicaTransaccion.length > 0){
                 var politica = _.head(context.config.politicaTransaccion);
                 var estadoPolitica = politica.estado_politica.estado2;
                 if(val == politica.estado_politica.estado2){
                   console.log("Entro en inpolitica");
                     context.disabledPorPolitica = context.inPolitica();
                 }else{
                     context.disabledPorPolitica = false;
                 }

              }*/
              //El cambio filtra cuales cumplen las condiciones de estado1 a estado 2, ya que puede haber n cantidad de politicas
              if(context.config.vista != 'crear' && context.config.politicaTransaccion.length > 0){

                 var politicas =  context.config.politicaTransaccion;

                  var aplica = _.filter(politicas, function(politica) {
                      return politica.estado_politica.estado2 === val && oldVal===politica.estado_politica.estado1;
                  });

                  if(_.isEmpty(aplica)){
                    context.disabledPorPolitica = false;
                  }else{
                     context.disabledPorPolitica = context.inPolitica(aplica);
                  }
               }
           }
    },
    computed:{
      desHabilitandoPorCantidad: function desHabilitandoPorCantidad() {

         var context = this;
         context.config.disableDetalle = false;

            _.forEach(context.detalle.articulos, function(articulo){
                     if(parseFloat(articulo.cantidad) > parseFloat(articulo.cantidad_maxima)){
                      context.config.disableDetalle = true;
                       return false
                    }
              });
        }
     }

});

Vue.nextTick(function () {
    form_factura_compra.guardar();
});
