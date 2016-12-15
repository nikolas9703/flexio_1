
var articulo = Vue.component('articulo',{

    template:'#articulo_template',

    props:{

        config:Object,
        detalle:Object,
        catalogos:Object,
        parent_index:Number,
        row:Object,
        empezable:Object

    },

    data: function(){

        return {
            disabledArticulo:false,//se usa para inhabilitar miestras se espera respuesta del ajax
            fa_caret:'fa-caret-right',
            enableWatch:true
        };

    },

    ready:function(){

        var context = this;

    },

    watch: {

        'row.categoria_id':function(val, oldVal){

            this.getItems();

            if(this.config.enableWatch && this.enableWatch){

                this.row.cantidad = '';
                this.row.descripcion = '';
                this.row.cuenta_id = '';
                this.row.descuento = '';
                this.row.impuesto_id = '';
                this.row.item_id = '';
                this.row.precio_unidad = '';
                this.row.unidad_id = '';
                this.facturado = false;

            }


        },

        'row.item_id':function(val, oldVal){

            var context = this;
            var item = _.find(context.row.items,function(item){
                return item.id==context.row.item_id;
            });

            this.getUnidades();

            if(this.config.enableWatch && this.enableWatch){

                context.row.cantidad = '';
                context.row.descripcion = '';
                context.row.cuenta_id = '';
                context.row.descuento = '';
                context.row.impuesto_id = '';
                context.row.precio_unidad = '';
                context.row.unidad_id = '';
                context.row.facturado = false;

                if(!_.isEmpty(item)){

                    context.setItem(item);

                }

            }

        }

    },

    computed: {

        getPrecioUnidad:function(){

            var context = this;
            if(context.precioCompra() || context.row.id != ''){

                return context.row.precio_unidad;

            }

            var precio = _.find(context.row.precios, function(precio){
                return precio.id == context.detalle.item_precio_id;
            });

            if(!_.isEmpty(precio)){

                var unidad = _.find(context.row.unidades, function(unidad){
                    return unidad.id == context.row.unidad_id;
                });
                if(!_.isEmpty(unidad)){

                    return (parseFloat(precio.pivot.precio) || 0) *  (parseFloat(unidad.pivot.factor_conversion) || 0);

                }
                return parseFloat(precio.pivot.precio) || 0;

            }

            return 0;

        },

        getSubtotal: function () {

            var context = this;
            return context.row.cantidad * context.getPrecioUnidad;

        },

        getImpuestoTotal:function(){

            var context = this;
            var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                return impuesto.id==context.row.impuesto_id;
            });
            var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.impuesto) : 0;
            return ((context.getSubtotal - context.getDescuentoTotal) * aux)/100;

        },

        getRetenidoTotal:function(){

            var context = this;
            var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                return impuesto.id==context.row.impuesto_id;
            });
            var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.porcentaje_retenido) : 0;
            return ((context.getImpuestoTotal) * aux)/100;

        },

        getDescuentoTotal:function(){

            var context = this;
            return (context.getSubtotal * context.row.descuento)/100;

        }

    },

    methods:{
        precioCompra:function(){

            var context = this;
            var modulos_compras = ['ordenes','facturas_compras'];
            return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

        },

        precioVenta:function(){

            var context = this;
            var modulos_ventas = ['cotizaciones'];
            return modulos_ventas.indexOf(context.config.modulo) != -1 ? true : false;

        },

        changeCaret:function(){

            if(this.fa_caret === 'fa-caret-right'){
                this.fa_caret = 'fa-caret-down';
            }else{
                this.fa_caret = 'fa-caret-right';
            }

        },

        getUnidades: function () {

            var context = this;
            var item = _.find(context.row.items, function(item){

                return item.id==context.row.item_id;

            });

            if(_.isEmpty(item)){

                context.row.unidades = [];

            }else{

                context.row.unidades = JSON.parse(JSON.stringify(item.unidades));//inmutable

            }

        },


        getItems: function () {
            console.log('getItems');
            var context = this;
            var categoria = _.find(context.catalogos.categorias, function(categoria){

                return categoria.id==context.row.categoria_id;

            });

            if(!_.isEmpty(categoria)){

                if(!_.isEmpty(categoria.items)){
                    context.row.items = JSON.parse(JSON.stringify(categoria.items));//inmutable
                    Vue.nextTick(function(){
                        context.enableWatch = false;
                        context.setItemId();
                        Vue.nextTick(function(){

                            context.setUnidadId();
                            context.enableWatch = true;

                        });
                    });

                }else{
                    if(context.disabledArticulo == false){
                        context.getItemsAjax(categoria);
                    }
                }
                return;
            }
            context.row.items = [];

        },

        getItemsAjax:function(categoria){

            var context = this;
            var datos = $.extend({erptkn: tkn},categoria,{'ventas':context.precioVenta() ? 1 : 0});
            context.disabledArticulo = true;
            this.$http.post({
                url: window.phost() + "inventarios/ajax-get-items-categoria",
                method:'POST',
                data:datos
            }).then(function(response){

                if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){

                    categoria.items = JSON.parse(JSON.stringify(response.data.items));//inmutable
                    context.row.items = JSON.parse(JSON.stringify(response.data.items));//inmutable
                    context.disabledArticulo = false;

                    context.enableWatch = false;
                    context.setItemId();
                    Vue.nextTick(function(){

                        context.setUnidadId();
                        context.enableWatch = true;

                    });

                }

            }).catch(function(err){
                window.toastr['error'](err.statusText + ' ('+err.status+') ');
            });

        },

        setItemId:function(){

            var context = this;
            context.row.item_id = context.row.item_hidden_id;

        },

        setUnidadId:function(){

            var context = this;
            context.row.unidad_id = context.row.unidad_hidden_id;

        },

        setItem:function(item){

            var context = this;
            context.row = $.extend(context.row,JSON.parse(JSON.stringify(item)));
            context.row.id = '';

        },

        addRow:function(){

            this.detalle.articulos.push({
                id:'',
                cantidad: '',
                categoria_id: '',
                cuenta_id: '',
                descuento: '',
                impuesto_id: '',
                item_id: '',
                items:[],
                precio_total: '',
                precio_unidad: '',
                precios:[],
                unidad_id: '',
                unidades:[],
                descripcion: '',
                atributos:[],
                atributo_text:'',
                atributo_id:'',
                facturado:false
            });

        },

        removeRow:function(row){

            this.detalle.articulos.$remove(row);

        }

    }

});
