<?php
$cuentas = is_null($cuentas)?[]:$cuentas;
?>
<template id="items_entradas">
  <table id="notaCreditoItemTable" class="table table-striped">
    <thead>
      <tr>
        <!-- <th width="50%">Descripci&oacute;n</th> -->
        <th width="25%">Items</th>
        <th width="25%">Cuenta</td>
        <th width="15%">Monto</td>
        <th width="15%">Cr&eacute;dito</th>
        <th width="5%"></th>
      </tr>
    </thead>
    <tbody>
      <tr id="items{{$index}}" class="item-listing" v-for="item in items">
        <td>
          <input type="hidden" name="items[{{$index}}][id]" id="id{{$index}}" value="{{item.id}}">
          <input type="hidden" name="items[{{$index}}][item_id]" id="item_id{{$index}}" value="{{item.inventario_item.id}}">
          <input type="hidden" name="items[{{$index}}][impuesto_id]" id="impuesto_id{{$index}}" value="{{item.impuesto.id}}">
          <input type="hidden" name="items[{{$index}}][impuesto_total]" id="impuesto_total{{$index}}" value="{{item.impuesto_total}}">
          <input type="text" name="" :disabled="true" id="item_nombre{{$index}}" v-model="item.inventario_item.nombre" class="form-control" data-rule-required="true">
        </td>
        <td>
            <select :disabled="itemDisable" name="items[{{$index}}][cuenta_id]" id="cuenta_id{{$index}}" class="form-control select2" data-rule-required="true" v-model="item.cuenta_id">
                <option value="">Seleccione</option>
                <?php foreach($cuentas as $cuenta){ ?>
                  <option value="<?php echo $cuenta->id?>"><?php echo $cuenta->codigo ." " .$cuenta->nombre?></option>
                <?php } ?>
            </select>
        </td>
        <td><div class="input-group">
          <span class="input-group-addon">$</span>
            <input type="text" :disabled="true" name="items[{{$index}}][precio_total]" id="monto{{$index}}" v-model="item.precio_total | redondeo" class="form-control moneda" data-rule-required="true">
        </div></td>
        <td><div class="input-group">
          <span class="input-group-addon">$</span>
            <input type="text" name="items[{{$index}}][monto]" id="monto{{$index}}" v-model="item.monto | redondeo" class="form-control moneda" @keyup="calcular(item.monto,$index)" data-rule-required="true">
        </div></td>
        <td>
        <button  type="button" v-show="$index !== 0" class="hide btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="rows.length === 1 ?'':deleteRow(item)"><i class="fa fa-trash"></i></button></td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="no-line">
        <td class="no-line" colspan="3"></td>
        <td class="no-line">
              <input type="hidden" name="campo[subtotal]" value="{{subtotal}}">
             <div class="fila-total1">
               <div class="posicion text-left titulo-total">
                  Subtotal
               </div>
              <div class="posicion monto-total text-right" v-text="subtotal | moneda">

              </div>
          </div>
        </td>
        <td class="no-line"></td>
      </tr>
      <tr class="no-line">
        <td class="no-line" colspan="3"></td>
        <td class="no-line">
              <input type="hidden" name="campo[impuesto]" value="{{impuesto}}">
             <div class="fila-total1">
               <div class="posicion text-left titulo-total">
                  Impuesto
               </div>
              <div class="posicion monto-total text-right" v-text="impuesto | moneda">

              </div>
          </div>
        </td>
        <td class="no-line"></td>
      </tr>
      <tr>
        <td class="no-line" colspan="3"></td>
        <td class="no-line">
              <input type="hidden" name="campo[total]" value="{{total}}">
             <div class="fila-total1">
               <div class="posicion text-left titulo-total">
                  Total
               </div>
              <div class="posicion monto-total text-right" v-text="total | moneda">

              </div>
          </div>
        </td>
        <td class="no-line"></td>
      </tr>
    </tfoot>
  </table>
</template>
