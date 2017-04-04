
Vue.component('detalle',{

    template:'#detalle_template',

    props:{

        config:Object,
        detalle:Object,
        catalogos:Object

    },

    watch:{

        'detalle.proveedor_id':function(val, oldVal){
             if(val == ''){
                return '';
            }

            var context = this;
            var datos = $.extend({erptkn: tkn},{proveedor_id:val});
            this.$http.post({
                url: window.phost() + "proveedores/ajax-get-montos",
                method:'POST',
                data:datos
            }).then(function(response){
                 if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){

                    if(context.config.vista == 'crear'){
                        context.detalle.terminos_pago = response.data.termino_pago;
                        context.detalle.credito = response.data.credito;
                        context.detalle.saldo = response.data.saldo;
                    }

                }
            }).catch(function(err){
                window.toastr['error'](err.statusText + ' ('+err.status+') ');
            });

        }

    },

    ready:function(){

        if(this.config.vista == 'crear'){

            this.detalle.estado = '1';//por aprobar

        }

    },

    data:function(){

        return {



        };

    },
    computed:{
        tienePoliticas:function(){

            var politicas = this.config.politicaTransaccion;

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

           var categoriaArticuloPolitica2 = _.map(_.filter(colecion_articulos,function(articulo){
               return  _.some(politica_categoria, { 'categoria_id': parseInt(articulo.categoria_id) });
           }),function(a){ return {categoria_id:parseInt(a.categoria_id),precio_total: a.precio_total};});


            if(this.detalle.creado_por == usuario_id && this.detalle.estado == 1){
              console.log("creado por y estado por aprobar");
              this.config.disableArticulos = false;
              this.config.disableDetalle = false;
              setTimeout(function(){
              $('#estado').attr('disabled', true);
            }, 400);
            }else{
              console.log("NO creado por y estado por aprobar");
              this.config.disableArticulos = true;
              this.config.disableDetalle = true;
              setTimeout(function(){
              $('[name=typeahead]').attr('disabled', true);
            }, 400);
            }
            if(categoriaArticuloPolitica2.length != 0){
              console.log("Tiene permisos para editar");
              this.config.disableArticulos = false;
              this.config.disableDetalle = false;
              setTimeout(function(){
              $('[name=typeahead]').attr('disabled', false);
              $('#estado').attr('disabled', false);
            }, 400);
            return true;
          }
            return false;

        }

    }



});
