
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

            var context = this;
            if(context.responseWaiting)return;


            if(val == ''){
                this.detalle.saldo_proveedor = 0;
                this.detalle.credito_proveedor = 0;
                if(typeof window.orden_multiple == 'undefined'){
                  this.config.disableEmpezarDesde = false;
                }
                return '';
            }

            var datos = $.extend({erptkn: tkn},{proveedor_id:val});
            context.responseWaiting = true;
            this.$http.post({
                url: window.phost() + "proveedores/ajax-get-montos",
                method:'POST',
                data:datos
            }).then(function(response){

                context.responseWaiting = false;
                if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){

                    context.detalle.saldo_proveedor = response.data.saldo;
                    context.detalle.credito_proveedor = response.data.credito;
                    if($('#empezable_type option:selected').val() == 'ordencompra' || $('#empezable_type option:selected').val() == 'subcontrato'){
                      if(typeof window.orden_multiple == 'undefined'){
                        context.config.disableEmpezarDesde = false;
                      }
                      $('#proveedor_id').attr('disabled', 'disabled');
                    }else{
                      context.config.disableEmpezarDesde = true;
                    }
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
            select2_usuarios:{url: phost() + 'usuarios/ajax-catalogo',using:['id', 'nombre']},
            select2_bodega:{url: phost() + 'bodegas/ajax_catalogo',using:['id', 'nombre']},

            responseWaiting: false

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


Vue.directive('select2-catalog',
    {
        previousValue:null,
        twoWay: true,
        data:[],
        select2:{

        },
        //priority: 1000,
        params: ['config','options'],

        bind: function () {
            console.log("databinding", this.vm);
            var self = this;
            console.log("bind::",this)
            if(this.params.config==null){
                console.log("Parameter config is null select catalog");
                return;
            }
            this.select2={
                width:'100%',
                ajax: {
                    url: self.params.config.url,
                    method:'POST',
                    dataType: 'json',
                    delay: 200,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            limit: 10,
                            erptkn: window.tkn
                        };
                    },
                    processResults: function (data, params) {
                        self.data=data;
                        if(self.params.config.using==null){
                            console.log("ERROR: using is missing in select2-catalog config");
                        }
                        let resultsReturn = data.map(resp=> [{'id': resp[self.params.config.using[0]],'text': resp[self.params.config.using[1]]}]).reduce((a, b) => a.concat(b),[]);
                        self.vm.$emit("select_result", data, self.el);
                        return {results:resultsReturn};
                    },
                    escapeMarkup: function (markup) { return markup; },
                }
            }
            $(this.el).select2(this.select2);

            this.previousValue=$(this.el).val();
        },

        update: function (value) {
            var self = this;

            if(value!=null && value != ""){
                var obj = this.data.find((q)=> q[self.params.config.using[0]] == value);

                if(typeof obj != "undefined"){
                    this.select2['data']=[{'id': obj[self.params.config.using[0]],'text': obj[self.params.config.using[1]]}];
                    this.select2['ajax']=null;
                    self.vm.$emit("selected", obj, self.el);
                }else{

                    self.vm.$http.post({
                        url: typeof self.params.config.url_find != "undefined"? self.params.config.url_find:self.params.config.url ,
                        method: 'POST',
                        data: {
                            id: value, // search term
                            erptkn: window.tkn
                        }}).then((response) => {
                        if(response!=null && response.data.length > 0){
                            self.vm.$emit("select_result", response.data, self.el);
                            obj=response.data[0];
                            self.select2['data']=[{'id': obj[self.params.config.using[0]],'text': obj[self.params.config.using[1]]}];
                           // self.select2['ajax']=null;
                            $(self.el).select2(self.select2).on('change', function(e) {
                                self.set($(self.el).val());
                            })
                            $(self.el).val(value).trigger('change');

                            self.vm.$emit("selected", obj, self.el);
                        }
                    });
                }
            }

            $(self.el).select2(self.select2).on('change', function(e) {
                self.set($(self.el).val());
            })
            $(self.el).val(value).trigger('change');
        },

        unbind: function () {
            $(this.el).off().select2('destroy');
        }
    }

);
