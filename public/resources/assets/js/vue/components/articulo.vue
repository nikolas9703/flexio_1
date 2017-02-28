<template>
    <!-- USADO EN COTIZACION DE ALQUILER -->
    <tr :style="(row.facturado || configv.vista == 'crear') ? 'background:white;' : 'background:orange;'"
        class="animated"
        transition="listado">

        <td style='background: white;'>
            <i class="fa" :class="fa_caret" style="font-size: 28px;width: 10px;" @click="changeCaret"></i>
            <input type="hidden" name="items_alquiler[{{parent_index}}][id]" class="item_hidden" id="id{{parent_index}}"
                   value="{{row.id}}">
        </td>

        <td class="categoria{{parent_index}} ">
            <select name="items_alquiler[{{parent_index}}][categoria_id]" class="categoria select2"
                    id="categoria{{parent_index}}" data-rule-required="true" aria-required="true"
                    v-model="row.categoria_id" v-select3="row.categoria_id" :config="config.select2"
                    :disabled="config.disableArticulos || disabledArticulo" @change="getItems(row.categoria_id)">
                <option value="">Seleccione</option>
                <option :value="categoria.id" v-for="categoria in catalogos.categorias">{{categoria.nombre}}</option>
            </select>
        </td>

        <td class="item{{parent_index}} ">
            <input type="hidden" name="items_alquiler[{{parent_index}}][item_hidden]" class="item_hidden"
                   id="item_hidden{{parent_index}}" v-model="row.item_hidden">
            <input type="hidden" id="comentario{{parent_index}}" name="items_alquiler[{{parent_index}}][comentario]"
                   value="{{row.comentario}}">
            <div class="input-group">
                <input type="hidden" name="items_alquiler[{{parent_index}}][item_id]" class="item_hidden"
                       id="item{{parent_index}}" v-model="row.item_id">
                <typeahead :item_url="item_url"></typeahead>
                <span class="input-group-btn">
                <a id="boton{{parent_index}}" type="button" class="btn btn-default" rel=popover
                   v-item-comentario="row.comentario" :i="parent_index" :comentado="row.comentario"> <span
                        class="fa fa-comment"></span></a>
            </span>
            </div>
        </td>

        <td class="atributo{{parent_index}} ">
            <input type="text" name="items_alquiler[{{parent_index}}][atributo_text]" class="form-control atributo"
                   id="atributo_text{{parent_index}}" v-if="!tieneAtributos" v-model="row.atributo_text"
                   :disabled="config.disableArticulos || disabledArticulo">
            <select name="items_alquiler[{{parent_index}}][atributo_id]" class="atributo"
                    id="atributo_id{{parent_index}}" v-if="tieneAtributos" v-model="row.atributo_id"
                    v-select3="row.atributo_id" :config="config.select2"
                    :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="atributo.id" v-for="atributo in row.atributos">{{atributo.nombre}}</option>
            </select>
        </td>

        <td class="cantidad{{parent_index}}"> <!--:class="mostrar.icono_pop_over?'input-group':''" -->
            <input type="text" name="items_alquiler[{{parent_index}}][cantidad]" class="form-control cantidad valid"
                   data-rule-required="true" aria-required="true" id="cantidad{{parent_index}}" aria-required="true"
                   v-model="row.cantidad" v-inputmask3="row.cantidad" :config="config.inputmask.numero"
                   :disabled="config.disableArticulos || disabledArticulo">
            <span v-if="mostrar.icono_pop_over" class="input-group-addon cantidad_info"
                  style="background-color:#27AAE1;color:white;border:1px solid #27AAE1" v-pop_over_cantidad=""><i
                    class="fa fa-info"></i></span>
        </td>


        <td class="periodo_tarifario{{parent_index}} ">

            <select id="periodo_tarifario{{parent_index}}" name="items_alquiler[{{parent_index}}][periodo_tarifario]"
                    v-select3="row.periodo_tarifario" :config="config.select2" class="form-control select2"
                    data-rule-required="true" v-model="row.periodo_tarifario"
                    :disabled="disabledEditar || disabledEditarTabla">
                <option value="">Seleccione</option>
                <option value="{{ciclo.valor}}" v-for="ciclo in cargaPeriodosTarifarios">{{{ciclo.nombre}}}
                </option>
            </select>

        </td>

        <td class="precio_unidad{{parent_index}} ">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" name="items_alquiler[{{parent_index}}][precio_unidad]"
                       v-model="row.precio_unidad" class="form-control precio_total moneda"
                       id="precio_unidad{{parent_index}}"
                       agrupador="items">
                <input type="hidden" name="items_alquiler[{{parent_index}}][impuesto_total]"
                       value="{{getImpuestoTotal | currency ''}}" class="form-control impuesto_total"
                       id="impuesto_total{{parent_index}}">
                <input type="hidden" name="items_alquiler[{{parent_index}}][descuento_total]"
                       value="{{getDescuentoTotal | currency ''}}" class="form-control descuento_total"
                       id="descuento_total{{parent_index}}">
                <!-- <input type="hidden" name="items_alquiler[{{parent_index}}][retenido_total]" value="{{getRetenidoTotal | currency ''}}" class="form-control retenido_total" id="retenido_total{{parent_index}}" > -->
            </div>
        </td>

        <td style="background: white;">
            <button type="button" class="btn btn-default btn-block agregarBtn" agrupador="items"
                    label="<i class=&quot;fa fa-plus&quot;></i>" v-if="parent_index=='0'" @click="addRow()"
                    :disabled="config.disableArticulos || disabledArticulo|| config.disableAddRow"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-default btn-block eliminarBtn" agrupador="items"
                    label="<i class=&quot;fa fa-trash&quot;></i>" v-if="parent_index!='0'" @click="removeRow(row)"
                    :disabled="config.disableArticulos || disabledArticulo"><i class="fa fa-trash"></i></button>
            <input type="hidden" name="items_alquiler[{{parent_index}}][id_pedido_item]" value="" class="form-control"
                   id="id_pedido_item">
        </td>

    </tr>

    <tr v-show="fa_caret == 'fa-caret-down'">
        <td></td>
        <td colspan="6" style="padding:0">
            <table style="width: 100%;background: #A2C0DA;">

                <td class="impuesto_id{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>Impuesto</label>
                    <select name="items_alquiler[{{parent_index}}][impuesto_id]" class="impuesto select2"
                            id="impuesto_id{{parent_index}}" data-rule-required="true" aria-required="true"
                            v-model="row.impuesto_id" v-select3="row.impuesto_id" :config="config.select2"
                            :disabled="config.disableArticulos || disabledArticulo">
                        <option value="">Seleccione</option>
                        <option :value="impuesto.id" v-for="impuesto in catalogos.impuestos">{{impuesto.nombre}}
                        </option>
                    </select>
                </td>

                <td class="descuento{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>Descuento</label>
                    <div class="input-group" style="width: 100%;">
                        <input type="input-right-addon" name="items_alquiler[{{parent_index}}][descuento]"
                               class="form-control descuento" id="descuento{{parent_index}}" agrupador="items"
                               v-model="row.descuento" v-porcentaje="row.descuento"
                               :config="config.inputmask.porcentaje"
                               :disabled="config.disableArticulos || disabledArticulo">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>

                <td class="cuenta{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>{{config.vista == 'crear' ? 'Cuenta' : 'Cuenta'}}</label>
                    <select name="items_alquiler[{{parent_index}}][cuenta_id]" class="cuenta select2"
                            id="cuenta{{parent_index}}" data-rule-required="true" aria-required="true"
                            v-model="row.cuenta_id" v-select3="row.cuenta_id" :config="config.select2"
                            :disabled="config.disableArticulos || disabledArticulo">
                        <option value="">Seleccione</option>
                        <option :value="cuenta.id" v-for="cuenta in getCuentas">{{cuenta.codigo +' '+ cuenta.nombre}}
                        </option>
                    </select>
                </td>

            </table>
        </td>
        <td></td>
    </tr>

</template>

<script>
import Articulos from '../../../js/items';
export default {
    props:[
       'detalle',
       'catalogos',
       'parent_index',
       'row',
       'empezable',
       'mostrar',
       'desabilitar',
       'configv'
   ],
   data(){
        
       

       return {
           disabledArticulo:false,//se usa para inhabilitar miestras se espera respuesta del ajax
           tarifario_precios_items:[],//se usa para mostrar los precios que tiene precio mayor 0 de items
           fa_caret:'fa-caret-right',
           modulo:"none",
           //solo busca items de alquiler
           item_url:'ajax_catalogo/item_typehead',
           config:{
               select2:{width:"100%",placeholder: "Seleccione"},
               inputmask:{numero:'decimal'}
            }
       };

   },
   ready(){
       $(".moneda").inputmask('currency',{
            prefix: "",
            autoUnmask : true,
            removeMaskOnSubmit: true
          });
          $(".porcentaje").inputmask('percentage',{
            suffix: "",
            clearMaskOnLostFocus: false
          });
          this.tarifario_precios_items =this.catalogos.periodos_tarifario;
   },
   components:{
       'typeahead':require('./typeahead.vue')
   },
   computed:{
        
        cargaPeriodosTarifarios: function() {
            var context = this;
            this.row.catalogo_periodos_tarifario = this.catalogos.periodos_tarifario;
            var item = _.find(this.row.items, (query)=>{ return query.id==context.row.item_id;});
            
            if(typeof item != 'undefined')
            {
                if(item.precios.length>0){
                    
                        context.row.catalogo_periodos_tarifario = _.filter(context.tarifario_precios_items, function(periodo){
                            return typeof item.precios_alquiler[0]!='undefined' && typeof item.precios_alquiler[0][periodo.valor]!='undefined' && +item.precios_alquiler[0][periodo.valor]>0;
                        });
                    
                }
            }
            
            return context.row.catalogo_periodos_tarifario;
        },

       getCuentas:function()
       {
           var aux = [];
           var context = this;
           console.log(context.row.cuentas.length > 0 && typeof context.row.cuentas != 'undefined');
           if(context.row.cuentas.length > 0 && typeof context.row.cuentas != 'undefined')
           {
               var cuenta_ingreso = context.row.cuentas.match(/("ingreso:.*?")/gi);
               if(cuenta_ingreso==null){
                   return context.catalogos.cuentas;
               }

               var cuenta_ingreso = cuenta_ingreso.length > 0 ? cuenta_ingreso[0].replace(/"|ingreso:/g, "") : "";

               aux = _.filter(context.catalogos.cuentas, function(cuenta){
                   return cuenta.id==cuenta_ingreso;
               });

               if(aux != null){
                 setTimeout(function(){
                     context.row.cuenta_id = aux[0].id;
                 }, 300);
               }
           }

           return _.isEmpty(aux) ? context.catalogos.cuentas : aux;
       },

       getSubtotal() {
           var context = this;
           return context.row.cantidad * context.row.precio_unidad;
       },

       getImpuestoTotal()
       {

           var context = this;
           var impuesto = _.find(context.catalogos.impuestos,(impuesto)=>{ return impuesto.id==context.row.impuesto_id;});
           var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.impuesto) : 0;
           return ((context.getSubtotal - context.getDescuentoTotal) * aux)/100;

       },

       getRetenidoTotal(){
           var context = this;
           var impuesto = _.find(context.catalogos.impuestos,(impuesto)=>{ return impuesto.id==context.row.impuesto_id;});
           var aux = (!_.isEmpty(impuesto)) ? parseFloat(impuesto.porcentaje_retenido) : 0;
           return ((context.getImpuestoTotal) * aux)/100;
       },

       getDescuentoTotal(){
           var context = this;
           return (context.getSubtotal * context.row.descuento)/100;
       },
       tieneAtributos(){
           return this.row.atributos.length > 0;
       }
      },
   methods:{
       changeCaret:function(){

            this.fa_caret = this.fa_caret === 'fa-caret-right'? 'fa-caret-down':'fa-caret-right';
        },
        encontrarCategoria(){
            var context = this;
            return _.find(this.catalogos.categorias, (query)=>{ return query.id==context.row.categoria_id;});
        },
        encontrarIndexCategoria(categoria){
            return _.findIndex(this.catalogos.categorias, categoria);
        },
        setRow(){
           this.isCambiaItem();
           var context = this;
           var item = _.find(context.row.items, (query)=>{ return query.id==context.row.item_id;});
            // para utilizar los valores de editar
           if(!_.isInteger(context.row.item_hidden))
           {
           context.row.cuenta_id = item.cuenta_id;
           context.row.impuesto_id = item.impuesto_id;
           }
           context.$set('row.atributos',item.atributos || []);

       },
       addRow:function(){

           Articulos.items.push({
               id:'',
               cantidad: '1',
               categoria_id: '',
               cuenta_id: '',
               items:[],
               descuento: '',
               impuesto_id: '',
               item_id: '',
               precio_total: '',
               precio_unidad: '',
               atributos:[],
               atributo_text:'',
               atributo_id:'',
               periodo_tarifario:'',
               comentario:'',
               facturado:true,
               cuentas:'[]',
               en_alquiler:0,
               por_entregar:0,
               entregado:0,
               devuelto:0,
               catalogo_periodos_tarifario: []
           });

       },
       removeRow:function(row){
           if(row.por_entregar > 0 || row.entregado > 0){//se usa en contratos de alquiler
               toastr["error"]("El articulo se encuentra relacionado a una entrega.");
               return;
           }
           Articulos.items.$remove(row);
       },
       getItems:function(categoria_id) {

           if(!_.isInteger(this.row.item_hidden)){
             this.cambiarCategoria();
          }
          var context = this;
          var categoria = _.find(this.catalogos.categorias, (query)=>{ return query.id==context.row.categoria_id;});

          if(_.has(categoria,'items') && _.isEmpty(categoria.items)){
              context.getItemsAjax(categoria_id);
              return;
          }
          context.$broadcast('fill-typeahead',categoria.items);
          context.row.items = categoria.items;
          return categoria.items;


              /*if(context.disabledArticulo == false){
                  context.getItemsAjax(categoria);
              }*/

      },

      getItemsAjax:function(categoria_id){

          var context = this;
          var datos = $.extend({erptkn: tkn},{categoria_id:categoria_id});
          context.disabledArticulo = true;
          var ajaxUrl = "ajax_catalogo/ajax_get_items_categoria"
          var categoriaItems = this.ajaxPost(ajaxUrl,datos);
          var categoria = this.encontrarCategoria();
          var categoriaIndex = this.encontrarIndexCategoria(categoria);

          categoriaItems.then(function(response){

              if(_.has(response.data, 'session')){
                  window.location.assign(window.phost());
                  return;
              }

              if(!_.isEmpty(response.data)){

                  context.catalogos.categorias[categoriaIndex].items = response.data.items;
                  context.catalogos.categorias.$set(categoriaIndex,context.catalogos.categorias[categoriaIndex]);
                  Vue.nextTick(function(){
                      context.$set('row.items', context.catalogos.categorias[categoriaIndex].items);
                      context.$broadcast('fill-typeahead',response.data.items);

                      if(context.configv.vista ==='editar'){
                          if(_.isInteger(context.row.item_hidden)){
                              context.row.item_id = context.row.item_hidden;
                              context.$broadcast('set-typeahead-nombre',context.row.nombre);
                          }

                      }else{
                          context.setItemId();
                      }
                      context.isCambiaItem();

                  });
                  context.disabledArticulo = false;
              }
          });
      },

      setItemId:function(){

          var context = this;
          context.row.item_id = context.row.item_hidden;
          if(_.isInteger(context.row.item_hidden)){
              var item = _.find(context.row.items,['id',context.row.item_id]);
              if(! _.isUndefined(item)){
               context.$broadcast('set-typeahead-nombre',item.nombre);
             }
          }
      },

    ajaxPost(ajaxUrl,datos){
      return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
    },
    cambiarCategoria(){

        if(_.isEmpty(this.row.categoria_id))this.row.items = this.row.items ||[];
        this.row.item_id = this.row.item_id || '';
        this.row.atributos = [];
        this.row.atributo_id ='';
        this.row.cantidad = 1;
        this.row.precio_unidad = "0.00";
        this.row.descuento = '0.00';
        this.row.impuesto_id = '';
        this.row.cuenta_id = '';
        this.row.atributo_text = '';
        this.row.periodo_tarifario = ''
    },
    isCambiaItem(){
        var context=this;
        if(!_.isInteger(this.row.item_id) && !_.isInteger(this.row.item_hidden)){
            this.row.atributos = [];
            this.row.cuenta_id = '';
            this.row.impuesto_id = '';
             context.row.catalogo_periodos_tarifario =context.tarifario_precios_items;
        }else{
            var item = _.find(context.row.items, (query)=>{ return query.id==context.row.item_id;});
            if(typeof item != 'undefined')
            {
                if(item.precios.length>0){
                    Vue.nextTick(function() {
                        context.row.catalogo_periodos_tarifario = _.filter(context.tarifario_precios_items, function(periodo){
                            return typeof item.precios_alquiler[0]!='undefined' && typeof item.precios_alquiler[0][periodo.valor]!='undefined' && +item.precios_alquiler[0][periodo.valor]>0;
                        });
                    });
                }
            }
        }

        this.row.descuento = this.row.descuento ||'0.00';
        this.row.precio_unidad = this.row.precio_unidad || "0.00";
        this.row.atributo_text = this.row.atributo_text || '';
        this.row.atributo_id = this.row.atributo_id || '';
        this.row.periodo_tarifario = this.row.periodo_tarifario ||'';
    },
    setCantidad(cantidad){
        if(parseInt(cantidad) ===0)this.row.cantidad = 1;
    },
    setPrecioUnitario(precio){
        this.row.precio_unidad = precio;
    }

   },

   events: {

       'update-item':function(item)
        {

           this.row.items=[item];
           var selected_categoria = _.head(item.categoria);
           this.row.categoria_id = selected_categoria.id;
           this.row.item_id = item.id;
           this.row.item_hidden = item.id;
           this.row.cuenta_id = item.cuenta_id;
           this.row.atributos = item.atributos;
           this.row.impuesto_id = item.impuesto_id;
           this.row.cuentas = item.cuentas;
       },
       setTarifa:function(){

           var context = this;
           var item = _.find(context.row.items, (query)=>{ return query.id==context.row.item_id;});

           if(item==undefined){
              return;
           }

           var tipo_tarifa = context.row.periodo_tarifario;
           var lista_precio_alquiler_id = typeof this.$parent.detalle != 'undefined' && typeof this.$parent.detalle.lista_precio_alquiler_id != 'undefined' ? this.$parent.detalle.lista_precio_alquiler_id : typeof this.$parent.$parent.formulario.lista_precio_alquiler_id != 'undefined' ? this.$parent.$parent.formulario.lista_precio_alquiler_id : '';

           // Si existe variable lista de precio, Verificar si
           // Escoger la tarifa, segun la lista.

           if(typeof lista_precio_alquiler_id != 'undefined'){
             if(lista_precio_alquiler_id == ''){
                return;
             }

             var precio = _.find(item.precios_alquiler, function(precio){
                 return precio.id_precio==lista_precio_alquiler_id;
             });
             //#case1682.1 verificar si precio es undefined
             if (typeof precio!='undefined'){
                Vue.nextTick(function () {
                    context.row.precio_unidad = precio[tipo_tarifa];
                });
             }
          }
       }
   },

   watch:{

       'row.categoria_id'(val, oldVal){

           var context = this;

           //solo cuando id no esta vacio
           if(!_.isEmpty(val)){
               context.getItems(val);
               return;
           }

           context.cambiarCategoria();

       },

       'row.item_id'(val, oldVal){

           if(!_.isEmpty(val)){
             this.setRow();
             return;
         }
           this.isCambiaItem();
       },
       'row.periodo_tarifario'(val, oldVal){
           if(!_.isEmpty(val)){
             var scope = this;

             var lista_precio_alquiler_id = typeof this.$parent.detalle != 'undefined' && typeof this.$parent.detalle.lista_precio_alquiler_id != 'undefined' ? this.$parent.detalle.lista_precio_alquiler_id : typeof this.$parent.$parent.formulario.lista_precio_alquiler_id != 'undefined' ? this.$parent.$parent.formulario.lista_precio_alquiler_id : '';

             // Si existe lista de precio, Verificar si
             // Ya selecciono una lista de precio.
             if(typeof lista_precio_alquiler_id != 'undefined'){
               if(lista_precio_alquiler_id == ''){
                toastr.warning('Por favor seleccione una opcion de la Lista de Precio de Alquiler.');
                Vue.nextTick(function(){
                  scope.row.periodo_tarifario = '';
                });
                return;
              }
             }

             this.$emit('setTarifa');
             return;
           }

       }
   }

}



</script>
