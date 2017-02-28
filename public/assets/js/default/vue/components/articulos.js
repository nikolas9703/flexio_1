
Vue.component('articulos',{

    template:'#articulos_template',

    props:{

        config:Object,
        detalle:Object,
        catalogos:Object,
        empezable:Object

    },

    computed:{

        getSubTotal:function(){

            var context = this;
            var subtotal = _.sumBy(context.detalle.articulos, function(articulo){
                return context.getSubtotalArticulo(articulo);
            });

            return subtotal;
        },

        getImpuestoTotal:function(){

            var context = this;
            return _.sumBy(context.detalle.articulos, function(articulo){
                return context.getImpuestoArticulo(articulo);
            });

        },

        getRetenidoTotal:function(){


            var context = this;
            return _.sumBy(context.detalle.articulos, function(articulo){
                return context.getRetenidoArticulo(articulo);
            });

        },

        getDescuentoTotal:function(){

            var context = this;
            return _.sumBy(context.detalle.articulos, function(articulo){
                return context.getDescuentoArticulo(articulo);
            });

        },

        getTotal:function(){
            return this.getSubTotal + this.getImpuestoTotal - this.getDescuentoTotal;
        },

        showRetenido:function(){

            if(this.config.modulo != 'facturas_compras'){
                return false;
            }

            var context = this;
            var proveedor = _.find(context.catalogos.proveedores, function(proveedor){
                return proveedor.proveedor_id == context.detalle.proveedor_id;
            });
            if(context.config.modulo == 'facturas_compras' && this.getTotal > 0 && this.retieneImpuesto()){
                return true;
            }
            return false;

        }





    },

    methods:{

        retieneImpuesto:function(){
            var context = this;
            var proveedor = _.find(context.catalogos.proveedores, function(proveedor){
                return proveedor.proveedor_id == context.detalle.proveedor_id;
            });
            if(!_.isEmpty(proveedor) && proveedor.retiene_impuesto == 'no' && context.catalogos.empresa.retiene_impuesto == 'si'){
                return true;
            }
            return false;
        },
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

        getPrecioUnidad:function(articulo){

            var context = this;
            if(context.precioCompra() || articulo.id != ''){

                return articulo.precio_unidad;

            }

            var precio = _.find(articulo.precios, function(precio){
                return precio.id == context.detalle.item_precio_id;
            });

            if(!_.isEmpty(precio)){

                var unidad = _.find(articulo.unidades, function(unidad){
                    return unidad.id == articulo.unidad_id;
                });
                if(!_.isEmpty(unidad)){

                    return (parseFloat(precio.pivot.precio) || 0) *  (parseFloat(unidad.pivot.factor_conversion) || 0);

                }
                return parseFloat(precio.pivot.precio) || 0;

            }

            return 0;

        },

        getSubtotalArticulo: function (articulo) {

            return articulo.cantidad * this.getPrecioUnidad(articulo);

        },

        getDescuentoArticulo:function(articulo){

            return (this.getSubtotalArticulo(articulo) * articulo.descuento)/100;

        },

        getImpuestoArticulo:function(articulo){

            var context = this;
            var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                return impuesto.id==articulo.impuesto_id;
            });
            var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.impuesto) : 0;
            return ((context.getSubtotalArticulo(articulo) - context.getDescuentoArticulo(articulo)) * aux)/100;

        },

        getRetenidoArticulo:function(articulo){



            var context = this;
            var impuesto = _.find(context.catalogos.impuestos,function(impuesto){
                return impuesto.id==articulo.impuesto_id;
            });
            var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.porcentaje_retenido) : 0;
            return ((context.getImpuestoArticulo(articulo)) * aux)/100;

        },

        showPagos:function(){

            if(this.config.modulo == 'facturas_compras' && this.config.vista == 'editar'){
                return true;
            }
            return false;

        },

        showSaldo:function(){

            if(this.config.modulo == 'facturas_compras' && this.config.vista == 'editar'){
                return true;
            }
            return false;

        }


    },

    ready: function () {



    },

    data: function(){

        return {
            articulo:'articulo',
            total:0
        };

    }

});
