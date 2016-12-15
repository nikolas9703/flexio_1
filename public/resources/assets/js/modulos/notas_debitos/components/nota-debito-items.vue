
<template>
  <table id="notaDebitoItemTable" class="table table-striped tabla-dinamica">
    <thead>
      <tr>
        <th width="25%" v-html="(empezable.type != '') ? 'Items' : 'Descripci&oacute;n'"></th>
        <th width="25%">Cuenta</td>
        <th width="15%" v-show="(empezable.type != '') ? true : false">Monto</td>
        <th width="15%">D&eacute;bito</th>
        <th width="1%" style="background:white;" v-show="!(empezable.type != '') ? true : false"></th>
      </tr>
    </thead>
    <tbody>
      <tr id="items{{$index}}" class="item-listing" v-for="item in rows">
        <td>
          <input type="hidden" name="items[{{$index}}][id]" id="id{{$index}}" value="{{item.id}}">
          <input type="hidden" name="items[{{$index}}][item_id]" id="item_id{{$index}}" value="{{item.item_id}}">
          <input type="hidden" name="items[{{$index}}][impuesto_id]" id="impuesto_id{{$index}}" value="{{item.impuesto_id}}">
          <input type="hidden" name="items[{{$index}}][impuesto_total]" id="impuesto_total{{$index}}" value="{{get_total_impuesto($index)}}">
          <input :disabled="(empezable.type != '' || config.disableDetalle) ? true : false" type="text" name="items[{{$index}}][descripcion]" id="descripcion{{$index}}" v-model="item.descripcion" class="form-control" data-rule-required="true">
        </td>
        <td>
            <select name="items[{{$index}}][cuenta_id]" id="cuenta_id{{$index}}" class="form-control" data-rule-required="true" v-select2="item.cuenta_id" :config="config.select2" :disabled="(empezable.type != '' || config.disableDetalle) ? true : false">
                <option value="">Seleccione</option>
                <option :value="cuenta.id" v-for="cuenta in catalogos.cuentas">{{cuenta.codigo+ ' ' +cuenta.nombre}}</option>
            </select>
        </td>
        <td v-show="(empezable.type != '') ? true : false">
          <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" :disabled="true" name="items[{{$index}}][precio_total]" id="monto{{$index}}" value="{{item.precio_total | currency ''}}" class="form-control" data-rule-required="true">
          </div>
        </td>
        <td>
          <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" name="items[{{$index}}][monto]" id="monto{{$index}}" v-model="item.monto | currencyDisplay" class="form-control currencyDisplay" @keyup="calcular(item.monto,$index)" data-rule-required="true" :disabled="config.disableDetalle">
          </div>
        </td>
        <td v-show="!(empezable.type != '') ? true : false">
          <button  type="button" v-show="$index === 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="rows.length === 0 ?'':addRow()" :disabled="config.disableDetalle"><i class="fa fa-plus"></i></button>
          <button  type="button" v-show="$index !== 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="rows.length === 1 ?'':deleteRow(item)" :disabled="config.disableDetalle"><i class="fa fa-trash"></i></button>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="no-line">
        <td class="no-line" :colspan="(empezable.type != '') ? '3' : '2'"></td>
        <td class="no-line">
              <input type="hidden" name="campo[subtotal]" value="{{subtotal}}">
             <div class="fila-total1">
               <div class="posicion text-left titulo-total">
                  Subtotal
               </div>
              <div class="posicion monto-total text-right" v-text="subtotal | currencyDisplay">

              </div>
          </div>
        </td>
        <td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
      </tr>
      <tr class="no-line">
        <td class="no-line" :colspan="(empezable.type != '') ? '3' : '2'"></td>
        <td class="no-line">
              <input type="hidden" name="campo[impuesto]" value="{{impuesto}}">
             <div class="fila-total1">
               <div class="posicion text-left titulo-total">
                  Impuesto
               </div>
              <div class="posicion monto-total text-right" v-text="impuesto | currencyDisplay">

              </div>
          </div>
        </td>
        <td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
      </tr>
      <tr>
        <td :colspan="(empezable.type != '') ? '3' : '2'" class="no-line"></td>
        <td class="no-line">
              <input type="hidden" name="campo[total]" value="{{total}}">
             <div class="fila-total1">
               <div class="posicion text-left titulo-total">
                  Total
               </div>
              <div class="posicion monto-total text-right" v-text="total | currencyDisplay">

              </div>
          </div>
        </td>
        <td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
      </tr>
    </tfoot>
  </table>
</template>

<script>

export default {

  props:['rows','boton','error','catalogos', 'config', 'empezable', 'detalle'],

  data: function(){

    return {
      itemDisable:true
    };

  },

  computed:{

    total:function(){
      return this.subtotal + this.impuesto;
    },

    subtotal:function(){
      var subtotal = _.sumBy(this.rows,function(o){
        return parseFloat(o.monto) || 0;
      });
      return subtotal;
    },

    impuesto:function(){
      var impuesto = _.sumBy(this.rows,function(o){
        return parseFloat(o.impuesto_total) || 0;
      });
      return impuesto;
    }

  },

  methods:{

      addRow:function(){
        var context = this;
        context.rows.push({id:'', cuenta_id:'', monto:0, precio_total:0, descripcion: '', impuesto_total:0, impuesto_id:'', item_id:0});
      },

      deleteRow:function(row){
        var context = this;
        context.rows.$remove(row);
      },

      get_total_impuesto:function(index){
        return this.rows[index].impuesto_total;
      },

      get_porcentaje_impuesto: function(impuesto_id){
        var context = this;
        var impuesto = _.find(context.catalogos.impuestos, function(impuesto){
          return impuesto_id == impuesto.id;
        });

        return !_.isEmpty(impuesto) ? impuesto.impuesto : 0;
      },

      calcular:function(monto, index){

        var context = this;
        var operacionPorcentaje = new Operacion(monto,context.get_porcentaje_impuesto(this.rows[index].impuesto_id));
        this.rows[index].impuesto_total = operacionPorcentaje.porcentajeDelTotal();
        this.rows[index].monto = monto;
        this.rows.$set(index,this.rows[index]);

        if(context.empezable.id != '')
        {
          console.log(context.detalle.monto_factura, this.total);
          if(this.total > context.detalle.monto_factura){
            this.boton = true;
            this.error = "El total no puede ser mayor al monto de la factura";
          }else if(parseFloat(monto) > parseFloat(this.rows[index].precio_total)){
            this.boton = true;
            this.error = "El débito no puede ser mayor al monto del item";
          }else{
            this.boton = false;
            this.error = "";
          }
        }

      }

  }
};

</script>
