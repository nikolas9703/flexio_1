<template>
    <!--  USADO EN COTIZACION DE ALQUILER-->
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-noline tabla-dinamica itemsTable">
                    <thead>
                        <tr>
                            <th width="1%" style="background: white;"></th>
                            <th width="14%" class="categoria ">Categoría de item<span class="required" aria-required="true">*</span></th>
                            <th width="30%" class="item ">Item <span class="required" aria-required="true">*</span></th>
                            <th width="12%" class="atributo ">Atributo </th>
                            <th width="8%" class="cantidad ">Cantidad <span class="required" aria-required="true">*</span></th>
                            <th width="12%" class="unidad ">Periodo tarifario <span class="required" aria-required="true">*</span></th>
                            <th width="8%" class="precio_unidad ">Tarifa pactada <span class="required" aria-required="true">*</span></th>
                            <th width="1%" style="background: white;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!--componente articulo-->
                        <tr :catalogos="catalogos" v-for="row in detalle.articulos_alquiler"
                            :is="articulo"
                            :parent_index="$index"
                            :row.sync="row"
                            :mostrar="mostrar"
                            :desabilitar="desabilitar"
                            :configv="config"
                          ></tr>


                    </tbody>
                </table>
                <span class="tabla_dinamica_error"></span>

            </div>
        </div>
    </div>


</template>

<script>
//var columnas = require('./public/resources/assets/js/columnas.js')
import Articulos from '../../../js/items'
export default {
//mixins: [columnas],
 props:['config','catalogos', 'detalle'],
  data () {
    return {
      HedearColumnas: [],
      articulo:'articulo',
      //articulos:[],
      //catalogos:{categorias:this.catalogos.categorias, impuestos:this.catalogos.impuestos, cuentas:this.catalogos.cuentas,periodos_tarifario:this.catalogos.periodos_tarifario},
      mostrar:{icono_pop_over:false,icono_comentario:true},
      desabilitar:{selectItem:true}
    };
  },
  ready(){

console.log(this.detalle);
  },
  components:{
    'articulo':require('./articulo.vue')
  },
  computed:{

    getSubTotal:function(){

           var context = this;
           return _.sumBy(context.detalle.articulos_alquiler, function(articulo){
               return context.getSubtotalArticulo(articulo);
           });

       },

       getImpuestoTotal:function(){

           var context = this;
           return _.sumBy(context.detalle.articulos_alquiler, function(articulo){
               return context.getImpuestoArticulo(articulo);
           });
           return 0;
       },

       getRetenidoTotal:function(){

             var context = this;
             return _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                 return context.getRetenidoArticulo(articulo);
             });
        return 0;
        },

        getDescuentoTotal:function (){

             var context = this;
             return _.sumBy(context.detalle.articulos_alquiler, function(articulo){
                 return context.getDescuentoArticulo(articulo);
             });
            return 0;
        },

        getTotal:function (){

            return this.getSubTotal - this.getDescuentoTotal + this.getImpuestoTotal;

        }
 },
  methods:{

    setArticulosItems(){
        var self = this;
        //setTimeout(function(){
            self.$set('articulos',Articulos.items);
        //},1500);
    },
    getArticulosCatalogos(){
      var self = this;
      var filtros = {tipo_item:'alquiler',tipo_cuenta:'activo'};
      var datos = $.extend({erptkn: tkn},filtros);
      var urlCatalogo = 'ajax_catalogo/articulos_catalogos';
      var catalogo = this.ajaxPost(urlCatalogo,datos);

      catalogo.then((response)=>{
          self.$set('catalogos',response.data);

          Vue.nextTick(function(){
               setTimeout(function(){
                   self.$set('detalle.articulos_alquiler',Articulos.items);
               },500);
          });
      });
    },
    ajaxPost(ajaxUrl,datos){
        return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
    },
    getSubtotalArticulo: function (articulo) {

        return articulo.cantidad * articulo.precio_unidad;

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
}

};
</script>

<style type="text/css">
    .table-noline .table-noline td .totales_derecha{
        padding: 10px;
        border: 1px solid silver !important;
        border-left: 0px !important;
    }

    .table-noline .table-noline td .totales_izquierda{
        padding: 10px;
        border: 1px solid silver !important;
        border-right: 0px !important;
    }
    .text-red{
      color:red;
     font-weight: 700;
     margin-left:15px
    }
</style>
