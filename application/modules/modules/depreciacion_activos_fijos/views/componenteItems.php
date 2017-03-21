<template id="depreciacion_item">
  <table id="tablaItemDepreciacion" class="table table-striped">
   <thead>
     <tr>
       <th width="11.2%">Categoria de Item</th>
       <th width="11%">No. de Item</th>
       <th width="11.2%">Nombre de item</th>
       <th width="11.5%">Descripci&oacute;n</th>
       <th width="11%">No. de serie</th>
       <th width="11%">Valor Inicial</th>
       <th width="11%">Valor Actual</th>
       <th width="11%">Depreciaci&oacute;n</th>
       <th width="11%">Monto a depreciar</th>
     </tr>
   </thead>
   <tbody>
     <tr id="items{{$index}}" class="item-listing" v-for="item in productos">
       <td>{{item.categoria}}
         <input type="hidden" id="id{{$index}}" name="items[{{$index}}][id]" value="{{item.id}}">
       </td>
       <td>{{item.codigo}}<input type="hidden" id="item_id{{$index}}" name="items[{{$index}}][item_id]" value="{{item.item_id}}"></td>
       <td>{{item.nombre}}</td>
       <td>{{item.descripcion}}<input type="hidden" id="serial_id{{$index}}" name="items[{{$index}}][serial_id]" value="{{item.serial_id}}"></td>
       <td>{{item.codigo_serial}}<input type="hidden" id="codigo_serial{{$index}}" name="items[{{$index}}][codigo_serial]" value="{{item.codigo_serial}}">
       <input type="hidden" id="serial_id{{$index}}" name="items[{{$index}}][serial_id]" value="{{item.serial_id}}"></td>
       <td>{{item.valor_inicial | redondeo}}<input type="hidden" id="valor_inicial{{$index}}" name="items[{{$index}}][valor_inicial]" value="{{item.valor_inicial}}"></td>
       <td>{{item.valor_actual | redondeo}}<input type="hidden" id="valor_actual{{$index}}" name="items[{{$index}}][valor_actual]" value="{{item.valor_actual}}"></td>
       <td>
         <div class="input-group">
             <input type="text" name="items[{{$index}}][porcentaje]" id="depreciacion{{$index}}" v-model="item.porcentaje" class="form-control money" v-on:change="calculoDepreciacion(item.porcentaje,$index)">
             <span class="input-group-addon">%</span>
         </div>
       </td>
       <td>
         <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" name="items[{{$index}}][monto_depreciado]" id="monto_depreciado{{$index}}" v-model="item.monto_depreciado | redondeo" class="form-control money monto_depreciado" disabled>
         </div>
       </td>
     </tr>
   </tbody>

  </table>



</template>
