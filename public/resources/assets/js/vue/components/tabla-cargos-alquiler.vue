<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-noline tabla-dinamica itemsTable">
                    <thead>
                        <tr>
                            <th width="20%" class="item ">Item</th>
                            <th width="6%" class="cantidad ">Cantidad</th>
                            <th width="15%" class="atributo ">Rango de fechas</th>
                            <th width="8%" class="">Tarifa pactada</th>
                            <th width="8%" class="">Periodo tarifario</th>
                            <th width="8%" class="">Monto del periodo</th>
                            <th width="8%" class="">Cantidad de periodos</th>
                            <th width="8%" class="">Total</th>
                        </tr>
                    </thead>
                    <tbody v-if="detalle.articulos_alquiler.length<=0">
                      <tr>
                        <td colspan="8" align="center">{{{detalle.articulos_alquiler_loader}}}</td>
                      </tr>
                    </tbody>
                    <tbody v-for="row in detalle.articulos_alquiler" track-by="$index" v-show="detalle.articulos_alquiler.length>0">
              		    <tr>
              		        <td>
                            {{row.item.nombre}}
                            <input type="hidden" name="items_alquiler[{{$index}}][item_id]" v-model="row.item.id">
                          </td>
                          <td>
                            {{row.cantidad}}
                            <input type="hidden" name="items_alquiler[{{$index}}][cantidad]" v-model="row.cantidad">
                          </td>
                          <td>
                            {{row.tarifa_fecha_desde | moment}} {{row.tarifa_fecha_hasta != "" ? ' - ' : ""}} {{row.tarifa_fecha_hasta | moment}}
                            <input type="hidden" name="items_alquiler[{{$index}}][tarifa_fecha_desde]" v-model="row.tarifa_fecha_desde">
                            <input type="hidden" name="items_alquiler[{{$index}}][tarifa_fecha_hasta]" v-model="row.tarifa_fecha_hasta">
                          </td>
                          <td>
                            <div class="col-lg-12 label-item label-celeste">{{row.tarifa_pactada | currency}}</div>
                            <input type="hidden" name="items_alquiler[{{$index}}][tarifa_pactada]" v-model="row.tarifa_pactada">
                          </td>
                          <td>
                            {{{row.periodo.nombre}}}
                            <input type="hidden" name="items_alquiler[{{$index}}][tarifa_periodo_id]" v-model="row.periodo.id">
                          </td>
                          <td>
                            <div class="col-lg-12 label-item label-naranja">{{row.tarifa_monto | currency}}</div>
                            <input type="hidden" name="items_alquiler[{{$index}}][tarifa_monto]" v-model="row.tarifa_monto">
                          </td>
                          <td>
                            {{row.tarifa_cantidad_periodo}}
                            <input type="hidden" name="items_alquiler[{{$index}}][tarifa_cantidad_periodo]" v-model="row.tarifa_cantidad_periodo">
                          </td>
                          <td>
                            <div class="col-lg-12 label-item label-rojo">{{(row.tarifa_cantidad_periodo * row.tarifa_monto) | currency}}</div>
                            <input type="hidden" name="items_alquiler[{{$index}}][precio_total]" v-model="row.precio_total">
                            <input type="hidden" name="items_alquiler[{{$index}}][categoria]" value="{{row.categoria_id}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][unidad]" value="1">
                            <input type="hidden" name="items_alquiler[{{$index}}][impuesto_total]" value="{{row.impuesto_total}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][descuento_total]" value="{{row.descuento_total}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][retenido_total]" value="0">
                            <input type="hidden" name="items_alquiler[{{$index}}][impuesto]" value="{{row.impuesto_id}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][descuento]" value="{{row.descuento}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][cuenta]" value="{{row.cuenta_id}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][atributo_id]" value="{{row.atributo_id}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][atributo_text]" value="{{row.atributo_text}}">
                            <input type="hidden" name="items_alquiler[{{$index}}][precio_unidad]" value="{{row.tarifa_monto}}">
                          </td>
                      </tr>
                  </tbody>
                </table>
                <span class="tabla_dinamica_error"></span>

            </div>
        </div>
    </div>


</template>

<script>
export default {
  props:['detalle','config','empezable', 'articulos'],
  ready(){
      Vue.filter('moment', function (date, format) {
        if (!date) {
          return ''
        }
        return moment(date, 'YYYY-MM-DD').format('DD/MM/YY');
      });
  },
  data () {
    return {
      cargos: []
    };
  },
  components:{
  },
  watch:{
    'empezable.id':function(val, oldVal){

        if(val=='' || typeof uuid_orden != "undefined"){
          return false;
        }

        var scope = this;
        Vue.nextTick(function(){
          scope.detalle.articulos_alquiler_loader = 'Cargando...';
        });

        scope.enableWatch = false;
        var datos = $.extend({erptkn: tkn},{contrato_id:val, vista: typeof uuid_orden != "undefined" ? 'editar' : 'crear'});
        this.$http.post({
            url: window.phost() + "cargos/ajax-get-cargos",
            method:'POST',
            data:datos
        }).then(function(response){

            if(_.has(response.data, 'session')){
                window.location.assign(window.phost());
                return;
            }
            if(!_.isEmpty(response.data)){

                Vue.nextTick(function(){

                  scope.detalle.articulos_alquiler = response.data.items;
                  scope.detalle.articulos_alquiler_loader = '';

                  if(response.data.items.length <=0){
                    toastr['warning']('El contrato seleccionado aun no tiene cargos registrados.');
                  }
                });
            }else{
              Vue.nextTick(function(){
                //toastr['warning']('');
              });
            }
        }).catch(function(err){
            window.toastr['error'](err.statusText + ' ('+err.status+') ');
        });


        //Info del contrato
        this.$http.post({
            url: window.phost() + "contratos_alquiler/ajax-contrato-info",
            method:'POST',
            data:datos
        }).then(function(response){

            if(_.has(response.data, 'session')){
                window.location.assign(window.phost());
                return;
            }
            if(!_.isEmpty(response.data)){

                Vue.nextTick(function(){

                  scope.detalle.centro_contable_id = typeof response.data.centro_contable_id != 'undefined' ? response.data.centro_contable_id : "";
                  scope.detalle.centro_facturacion_id = typeof response.data.centro_facturacion_id !='undefined' ? response.data.centro_facturacion_id : "";
                  scope.detalle.creado_por = typeof response.data.created_by != 'undefined' ? response.data.created_by : "";
                  scope.detalle.precio_alquiler_id = typeof response.data.lista_precio_alquiler_id != 'undefined' ? response.data.lista_precio_alquiler_id : "";

                });
            }else{
              Vue.nextTick(function(){
                //toastr['warning']('');
              });
            }
        })

    }
  }
};
</script>

<style type="text/css">
    .text-red{
      color:red;
     font-weight: 700;
     margin-left:15px
    }
    .label-item{
      border:2px solid;
      text-align: center;
      font-weight: bold;
    }
    .label-celeste{
      border-color:#69ABD3;
      color: #69ABD3;
    }
    .label-naranja{
      border-color:#E59057;
      color: #E59057;
    }
    .label-rojo{
      border-color: #C94242;
      color: #C94242;
    }
</style>
