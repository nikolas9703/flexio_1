<template>

    <div class="row">
        <div class="col-lg-12">

            <div class="table-responsive">
                <table id="itemsTable" class="table table-noline tabla-dinamica itemsTable">
                    <thead>
                        <tr>
                            <th width="1%" style="background: white;" v-if="precioCompra() || precioInventario() || precioVenta()"></th>
                            <th width="12%" class="categoria ">Categoría<span class="required" aria-required="true" v-if="disableValidate">*</span></th>
                            <th width="30%" class="item ">Item <span class="required" aria-required="true" v-if="disableValidate">*</span></th>
                            <th width="12%" class="atributo ">Atributo </th>
                            <th width="8%" class="cantidad ">Cantidad <span class="required" aria-required="true" v-if="disableValidate">*</span></th>
                            <th width="8%" class="unidad ">Unidad <span class="required" aria-required="true" v-if="disableValidate">*</span></th>
                            <th width="8%" class="precio_unidad " v-if="precioCompra() || precioInventario() || precioVenta()">Precio unidad <span class="required" aria-required="true" v-if="disableValidate">*</span></th>
                            <th width="8%" class="precio_total " v-if="precioCompra() || precioInventario() || precioVenta()">Subtotal </th>
                            <th width="12%" class="cuenta " v-if="config.modulo != 'traslados' && !( precioCompra() || precioInventario() || precioVenta() )">Cuenta </th>
                            <th width="1%" style="background: white;padding: 0;padding-right: 8px;">
                                <button type="button" class="btn btn-default btn-block agregarBtn" agrupador="items"  @click="addRow()" :disabled="config.disableArticulos || config.disableAddRow"><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <!--componente articulo-->
                        <tr v-for="row in detalle.articulos" :is="articulo" :config="config" :detalle.sync="detalle" :catalogos="catalogos" :parent_index="$index" :row.sync="row" :empezable.sync="empezable"></tr>

                    </tbody>
                 </table>

                 <totales-inside v-if="config.modulo != 'ordenes_alquiler'" :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></totales-inside>

                <span class="tabla_dinamica_error"></span>

            </div>
        </div>
    </div>

</template>
<script>
/*
  usado en ordenes de compras

 */
import items from './../../config/lines_items';
export default {
    data: function(){

        return {
            articulo:'articulo',
            articulos:items
        };

    },
    props:{
          config:Object,
          detalle:Object,
          catalogos:Object,
          empezable:Object
    },
      components:{
          'articulo': require('./tabla-fila.vue'),
          'totales-inside':require('./tabla-totales.vue')
      },

      events:{

          eAddRow:function(){
                this.addRow();
          }

      },

      methods:{

          addRow:function(){

              this.detalle.articulos.push({
                  id:'',
                  cantidad: 1,
                  categoria_id: '',
                  cuenta_id: '',
                  cuentas:'[]',
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
                  facturado:false,
                  seriales:[],
                  tipo_id:'',
                  cantidad_maxima:''
              });

          },

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

          precioInventario:function(){

              var context = this;
              var modulos_compras = ['ajustes'];
              return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

          },

          precioCompra:function(){

              var context = this;
              var modulos_compras = ['ordenes','facturas_compras'];
              return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

          },

          precioVenta:function(){

              var context = this;
              var modulos_ventas = ['cotizaciones','ordenes_ventas', 'ordenes_alquiler'];
              return modulos_ventas.indexOf(context.config.modulo) != -1 ? true : false;

          },

          getPrecioUnidad:function(articulo){

              var context = this;
              if(context.precioCompra() || context.precioInventario() || articulo.id != '' || (typeof context.config.editarPrecio !== "undefined" && context.config.editarPrecio)){

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

      computed: {
        disableValidate: function (){
          var scope =  this;
          if(typeof this.detalle.cargos_adicionales_checked != 'undefined'){
            return this.detalle.cargos_adicionales_checked === 'true' ? true : false;
          }else{
            return true;
          }
        }
      }
}
</script>
<style>
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
</style>
