<template id="devolucion-items">
   <table class="table table-noline" id="devolucionDinamica" v-show="productos.length > 0">
    <thead>
      <tr>
      <th width="24%">No.del item o nombre</th>
      <th width="6%">Cantidad en factura</th>
      <th width="6%">Cantidad a devolver</th>
      <th width="12%">Unidad</th>
      <th width="12%">Precio Unidad</th>
      <th width="12%">Descuento</th>
      <th width="12%">Cuenta</th>
      <th width="12%">Precio Total</th>
    </tr>
    </thead>
    <tbody>

    <tr id="items{{$index}}" class="item-listing" v-for="item in productos">
    <td>
      <input type="hidden" id="devolucion_item_id{{$index}}" name="items[{{$index}}][devolucion_item_id]"  value="{{item.id}}">
      <input type="hidden" id="categoria_id{{$index}}" name="items[{{$index}}][categoria_id]"  value="{{item.categoria_id}}">
      <input type="hidden" id="impuesto_id{{$index}}" name="items[{{$index}}][impuesto_id]"  value="{{item.impuesto_id}}">
    <select data-placeholder="Seleccione" id="item_id{{$index}}" name="items[{{$index}}][item_id]" class="select2 form-control item-change" v-model="item.item_id" :disabled="camposDisable">
      <option value="">Seleccione</option>
      <option v-bind:value="item.inventario_item.id">
      {{ item.inventario_item.codigo + ' - ' + item.inventario_item.nombre }}</option>
    </select></td>

    <td>
      <input type="text" id="cantidad_factura{{$index}}" name="items[{{$index}}][cantidad]" class="form-control cantidad-item" data-rule-required="true"  v-model="item.cantidad"  :disabled="camposDisable">
    </td>
    <td>
      <input type="text" id="cantidad_devolucion{{$index}}" name="items[{{$index}}][cantidad_devolucion]" class="form-control cantidad-item" data-rule-required="true" v-on:keyup="validarCantidad(item.cantidad_devolucion,$index,$event)"  v-model="item.cantidad_devolucion">
    </td>
    <td>
      <select id="unidad_id{{$index}}"  name="items[{{$index}}][unidad_id]" class="form-control unidad-item"  v-model="item.unidad_id" :disabled="camposDisable">
      <option value="">Seleccione</option>
      <option v-for="unidad in item.inventario_item.unidades" v-bind:value="unidad.id">
      {{ unidad.nombre }}</option>
    </select>
  </td>
    <td>
    <div class="input-group">
         <span class="input-group-addon">$</span>
         <input type="text" id="precio_unidad{{$index}}" v-model="item.precio_unidad" name="items[{{$index}}][precio_unidad]" :disabled="camposDisable" class="form-control precio_unidad">
    </div>
    </td>
    <td>
    <div class="input-group">
        <input type="text" id="descuento{{$index}}" v-model="item.descuento" name="items[{{$index}}][descuento]" class="form-control item-descuento" value="0.00" placeholder="0.00" data-rule-required="true" data-inputmask="'mask':'9{1,3}[.*{1,2}]'" data-rule-range="[0,100]" :disabled="camposDisable">
        <span class="input-group-addon">%</span>
    </div>
    </td>
    <td><select id="cuenta_id{{$index}}" name="items[{{$index}}][cuenta_id]" class="form-control select2 item-cuenta" data-rule-required="true" v-model="item.cuenta_id">
      <option value="">Seleccione</option>
      <?php foreach($cuenta_activo as $cuenta) {?>
      <option value="<?php echo $cuenta->id?>"><?php echo $cuenta->nombre?></option>
      <?php }?>
    </select></td>
    <td>
     <div class="input-group">
         <span class="input-group-addon">$</span>
         <input type="input-left-addon" id="precio_total{{$index}}" name="items[{{$index}}][precio_total]" class="form-control precio_total" placeholder="0.00" :disabled="camposDisable" v-model="item.precio_total | currency ''">
     </div>
    </td>
    </tr>

    </tbody>
    <tfoot>
      <tr>
        <td colspan="7"></td>
        <td colspan="2"  class="sum-border"> <span>Subtotal: </span><span v-cloak class="sum-total">{{subtotales | currency}}</span></td>
        <td><input type="hidden" name="campo[subtotal]" id="hsubtotal" value="{{subtotales}}"></td>
      </tr>
      <tr>
        <td colspan="7"></td>
        <td colspan="2" class="sum-border"><span>Impuestos:</span> <span class="sum-total" v-cloak>{{impuestos | currency}}</span></td>
        <td><input type="hidden" name="campo[impuestos]" id="himpuesto" value="{{impuestos}}"></td>
      </tr>
      <tr>
        <td colspan="7"></td>
        <td colspan="2" class="sum-border"><span>Total: </span> <span class="sum-total" v-cloak>{{totales | currency}}</span></td>
        <td><input type="hidden" name="campo[total]" id="htotal" value="{{totales}}"></td>
      </tr>
      <tr>
        <td colspan="9"><div v-html="error"></div></td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</template>
