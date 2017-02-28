Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
var bloqueandoGuardar = 0; //El boton Guardar x default, debe ir activado, independiente de las politicas, solo al mover el estado, activa o desactiva
var items = require('./../../config/lines_items.js');
Vue.directive('select2ajax', require('./../../vue/directives/select2ajax.vue'));
var form_orden_compra = new Vue({

    el: "#appOrdenventa",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\OrdenesCompra\\Models\\OrdenesCompra",
            comentable_id: '',

        },

        config: {

            vista: window.vista,
            politicaTransaccion:window.politica_transaccion,
            enableWatch:false,
            select2:{width:'100%'},
            datepicker2:{dateFormat: "dd/mm/yy"},
            inputmask:{

                cantidad: {'mask':'9{1,4}','greedy':false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,5}]','greedy':false}

            },
            disableEmpezarDesde:false,
            disableDetalle:false,
            disableBotonForEstado:true,
            disableArticulos:false,
            disableFecha:true,
            disableProveedor:false,
            envieFormulario:false,
            modulo:'ordenes',
            disableAddRow:false,
        },
        catalogos:{

            proveedores:window.proveedores,
            bodegas:window.bodegas,
            centros_contables:window.centros_contables,
            estados:window.estados,
            terminos_pago:window.terminos_pago,
            categorias:window.categorias,
            cuentas:window.cuentas,
            impuestos:window.impuestos,
            usuarios:window.usuarios,
            aux:{}

        },
        empezable:{
            label:'Empezar orden de compra desde',
            type:'',
            types:[
                {id:'pedido',nombre:'Pedido'}//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
            ],
            id:'',
            pedidos:window.pedidos
        },
        detalle:{
            politicaTransaccion: 0,
            estado:1,//por aprobar
            fecha:moment().format('DD/MM/YYYY'), //requerido -> empezar_desde.js
            codigo: typeof window.codigo !== 'undefined' ? window.codigo : '', //requerido -> empezar_desde.js
            id:'',
            terminos_pago:'',
            recibir_en_id:'',
            centro_contable_id:'',
            proveedor_id:'',
            referencia:'',
            observaciones:'',
            creado_por: window.usuario_id,
            pagos:0,
            saldo:0,
            credito:0,
            valido_hasta: typeof window.valido_hasta !== 'undefined' ? window.valido_hasta :  moment().add(30, 'day').format('DD/MM/YYYY'),
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
        detalle_modal:{
          proveedor: '',
          correo: '',
          codigo: '',
          showModal: false
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
           context.config.disableProveedor=true,
            context.config.disableEmpezarDesde=true;
            context.detalle = JSON.parse(JSON.stringify(window.orden));
            context.catalogos.aux = JSON.parse(JSON.stringify(window.orden));
            context.empezable = $.extend(JSON.parse(JSON.stringify(window.empezable)), {label:context.empezable.label,types:context.empezable.types});
            if(context.empezable.type =='pedido'){
                context.config.disableAddRow = true;
            }
            if(context.detalle.estado == 2){
                context.catalogos.estados.splice(0,1);
                context.catalogos.estados.splice(1,1);
                context.catalogos.estados.splice(1,1);
            }

            if(context.detalle.estado == 1){
                //context.catalogos.estados.splice(2,1);
                context.catalogos.estados.splice(2,1);
                context.catalogos.estados.splice(2,1);
                context.config.disableProveedor = false;
            }

            if(context.detalle.estado > 3){

                context.config.disableDetalle = true;
                context.config.disableArticulos = true;

            }
            //Informacion para el modal, al momento de enviar el correo al proveedor
              context.detalle_modal.codigo = context.detalle.codigo;
              context.detalle_modal.correo = context.detalle.proveedor_info.email;
              context.detalle_modal.proveedor = context.detalle.proveedor_info.codigo;

              context.catalogos.proveedores = [context.detalle.proveedor_info];
            Vue.nextTick(function(){
                context.detalle.proveedor_id = context.detalle.proveedor_info.uuid_proveedor;
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.orden.comentario));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.orden.id));
                context.empezable.id = JSON.parse(JSON.stringify(window.empezable.id));

            });




        }
        Vue.nextTick(function(){

            context.config.enableWatch = true;
            if(context.config.vista == 'crear'){

                context.empezable.type = window.empezable.type;
                Vue.nextTick(function(){

                    context.empezable.id = window.empezable.id;

                });

            }

        });
        Vue.nextTick(function(){

        if(context.config.disableDetalle == true){
              toastr.info("Su rol no tiene permisos para el cambio de estado", "Mensaje");

          }

        });
        //if(context.config.vista == 'crear'){
         this.selectProveedores();
        //}
    },
    watch: {

        'detalle.centro_contable_id':function(val, oldVal){
            var context = this;
            if(context.config.vista == 'crear'){
                _.forEach(context.detalle.articulos, function(articulo){
                    articulo.cuenta_id = '';
                });
            }
        },

        'empezable.id': function(val, oldVal){

            var context = this;
            if(context.config.vista == 'crear')
            {
                context.getEmpezableAjax(context.empezable);
                if(context.empezable.type =='pedido'){
                    context.config.disableAddRow = true;
                }
            }

        },
                               //5,2
        'detalle.estado': function (val, oldVal) {
              var context = this;
             var categorias_permitidas = [];
             context.config.disableBotonForEstado = true; //Se desbloquea x el momento el boton
             if(context.config.vista != 'crear' && context.config.politicaTransaccion.length > 0){
                 var politica = _.head(context.config.politicaTransaccion);
                 var estadoPolitica = politica.estado_politica.estado2;
                 if(val == politica.estado_politica.estado2){
                     context.disabledPorPolitica = context.inPolitica();
                 }else{
                     context.disabledPorPolitica = false;
                 }

              }

           },
    },
    methods:{
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

                   let resultados = data.map(resp=> [{'id': resp.id,'text': resp.nombre}]).reduce((a, b) => a.concat(b),[]);
                     return {
                          results:resultados
                     }
                },
                escapeMarkup: function (markup) { return markup; },
            }
        });
        },

        getEmpezableAjax: function(empezable){

            var context = this;
            var datos = $.extend({erptkn: tkn},{'id':empezable.id,'type':empezable.type});

            context.config.enableWatch = false;
            context.$http.post({
                url: window.phost() + "ordenes/ajax-get-empezable",
                method:'POST',
                data:datos
            }).then(function(response){

                if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){

                    context.detalle = $.extend(context.detalle,JSON.parse(JSON.stringify(response.data)));
                    context.detalle.id = '';
                    Vue.nextTick(function(){
                        context.config.enableWatch = true;
                    });

                }
            });


        },

        enviarFormulario:function(enviar_orden_compra_correo){

            var context = this;
            context.config.envieFormulario = true;
            if(!enviar_orden_compra_correo)
            {
                context.detalle_modal.correo = '';
            }

            Vue.nextTick(function () {
                $('#myModal').modal('hide');
                $("#crearOrdenesForm").submit();
            });

        },

        guardar: function () {
            var context = this;
            var $form = $("#crearOrdenesForm");
             $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    var self = $(element);

                    if(self.hasClass("cuenta")){
                        toastr.error("Verifique en los items el campo 'Cuenta', es obligatorio", "Mensaje");
                    }

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

                    if(context.detalle.estado == 2 && !context.config.envieFormulario){
                        $('#myModal').modal('show');
                    }else{
                        $('input, select').prop('disabled', false);
                        $('form').find(':submit').prop('disabled',true);
                        form.submit();
                    }
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
                return false;
            }

            //filtros respectiva categoria y montos ejemplo [{categoria_id: 1, monto_limite:"200.00"}]
            var modulo_politicas =_.flattenDeep(_.map(politicas,function(policy){

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
           }),function(a){ return {categoria_id:parseInt(a.categoria_id),precio_total: a.precio_total};});
            if(categoriaArticuloPolitica.length === 0){
              self.config.disableBotonForEstado = false;
              self.config.disableDetalle = true,
              self.config.disableArticulos = true,
              toastr["info"]("No tiene permiso para cambiar el estado");
              toastr.options = {
                 "closeButton": true,
                 "preventDuplicates": true,
                 "showDuration": "300",
                 "hideDuration": "0",
                 "timeOut": "7000",
                 "extendedTimeOut": "1000",
               };
                return false;
            }

            ///creo un array para comparar
            var objfiltradoCat = _.uniqWith(categoriaArticuloPolitica,function(a, b){

                if(a.categoria_id === b.categoria_id){
                  b.precio_total = parseFloat(a.precio_total) + parseFloat(b.precio_total);
                  return true;
                }

            });

            //var objfiltradoCat =

            var politica_aplicadas = _.filter(politica_categoria,function(value){
                return _.some(objfiltradoCat, { 'categoria_id': value.categoria_id });
            });

            var politica_aplicada = _.filter(politica_aplicadas,function(value,key){
                return  objfiltradoCat[key].precio_total > parseFloat(value.monto_limite);
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

    }

});

Vue.nextTick(function () {
    form_orden_compra.guardar();
});
