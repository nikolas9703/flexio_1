
Vue.component('detalle',{

    template:'#detalle_template',

    props:{

        config:Object,
        detalle:Object,
        catalogos:Object

    },

    ready:function(){

        if(this.config.vista == 'crear'){

            this.detalle.estado = '13';//por aprobar

        }

    },

    watch:{

        'detalle.proveedor_id':function(val, oldVal){


            if(val == ''){
                this.detalle.saldo_proveedor = 0;
                this.detalle.credito_proveedor = 0;
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

                    context.detalle.saldo_proveedor = response.data.saldo;
                    context.detalle.credito_proveedor = response.data.credito;
                    if(context.config.vista == 'crear'){
                        context.detalle.terminos_pago = response.data.termino_pago;
                    }

                }
            }).catch(function(err){
                window.toastr['error'](err.statusText + ' ('+err.status+') ');
            });

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

           var categoriaArticuloPolitica = _.map(_.filter(colecion_articulos,function(articulo){
               return  _.some(politica_categoria, { 'categoria_id': parseInt(articulo.categoria_id) });
           }),function(a){ return {categoria_id:parseInt(a.categoria_id),precio_total: a.precio_total};});


            //console.log(politicas.length);
            if(this.detalle.creado_por == usuario_id && this.detalle.estado == 13){

              this.config.disableArticulos = false;
              this.config.disableDetalle = false;
            }else{

              this.config.disableArticulos = true;
              this.config.disableDetalle = true;
              setTimeout(function(){
              $('[name=typeahead]').attr('disabled', true);
            }, 400);
            }
            if(categoriaArticuloPolitica.length != 0){

              this.config.disableArticulos = false;
              this.config.disableDetalle = false;
              setTimeout(function(){
              $('[name=typeahead]').attr('disabled', false);
              $('#estado').attr('disabled', false);
            }, 400);
            }

            return false;
        }

    }



});
