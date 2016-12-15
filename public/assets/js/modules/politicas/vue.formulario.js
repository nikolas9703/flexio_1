var form_politicas = new Vue({

    el: '#formularioPoliticas',

    data:{
        catalogos:{
            roles:window.roles,
            modulos:window.modulos,
            categorias:window.categorias,
            transacciones:window.transacciones,
            catalogosEstados: window.transacciones
        },
        detalle:{
            empresa_id:window.empresa_id
         },
         formulario:{nombre:'',role_id:'',modulo:'',politica_estado:'', categorias:[],monto_limite:'',estado_id:''}
     },

    ready:function(){
       $(".moneda").inputmask('currency',{ prefix: "", autoUnmask : true, removeMaskOnSubmit: true });
    },

    computed: {
        disabledEstado:function(){
            return this.formulario.modulo ==='';
        },
        showCategorias:function(){
            return !_.includes(['pago','anticipo'], this.formulario.modulo);

        },
        catalogoDinamicoEstados:function(){


            if(_.isEmpty(this.formulario.modulo)){
                return [];
            }
            var self = this;
           var catalogo =  _.filter(this.catalogos.catalogosEstados,function(query){
               return query.tipo === self.formulario.modulo;});
               //Card 1320 se cambio el find por el filter, por que un modulo puede tener multiples transacciones de estados

            if(!_.isUndefined(catalogo)){
                 return catalogo; // cambio por el Card 1320
                 //return [catalogo];
            }
            return [];
        }

    },
    watch: {},
    methods:{

       recargar: function(){
            var tablaUrl = phost() + 'politicas/ajax-listar';

                 var gridObj = $("#tablaPoliticasTransaccionesGrid");
                   gridObj.setGridParam({
                        url: tablaUrl,
                        datatype: "json",
                        postData: {
                            codigo: '',
                            cliente_id: '',
                            fecha_desde: '',
                            fecha_hasta: '',
                            estado_id: '',
                            erptkn: tkn
                        }
                    }).trigger('reloadGrid');
       },

       initialState: function(){
           var scope = this;
           var formulario = $(scope.$el);
           formulario.trigger("reset");
           $("#id").val("");
            $(".select2").select2({
                theme: "bootstrap",
                width: "100%"
            });
      },


      guardar: function () {

            var scope = this;
            var formulario = $(scope.$el);

            if (formulario.validate().form() == false) {
                //mostrar mensaje
                toastr.error('Debe completar los campos requeridos.');
                return false;
            }
            $(formulario).find('#guardarBtn').prop('disabled',true);
             Vue.http({
                url: phost() + 'politicas/ajax-guardar-politica',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: formulario.serializeObject()
            }).then(function (json) {



                if ($.isEmptyObject(json.data.session) == false) {
                    window.location = phost() + "login?expired";
                }
                $(formulario).find('#guardarBtn').prop('disabled',false);
                  if (json.data.response == true) {

                        toastr.success(json.data.mensaje);
                        scope.initialState();
                        scope.recargar();
                  }else{
                      toastr.error(json.data.mensaje);
                  }


            }, function (response) {
                // error callback
            });


         }

    }

});
/*
Vue.nextTick(function () {
    form_politicas.guardar();
});*/
