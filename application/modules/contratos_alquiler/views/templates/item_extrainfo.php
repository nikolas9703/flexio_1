
<template id="item_extrainfo">
    
        
    <table class="table" :id="'entrega_item_detalles' + parent_index" style="background: #DEEBF7">
        
        <tbody style="border: 1px solid white;">
            <tr class="item-listing"  :id="'item_extrainfo' + $index">
                <td width="33%" >
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Impuesto </label>
                          <select  :disabled="disabledEditar || disabledEditarTabla" data-rule-required="true" data-placeholder="Seleccione" v-model="parent_articulo.impuesto"  name="articulos[{{parent_index}}][impuesto]" class="form-control chosen-select"    >
                          	<option value="">Seleccione</option>
                           	<option value="{{impuesto.id}}"  v-for="impuesto in impuestos | orderBy 'nombre'" >{{impuesto.nombre}}</option>
                         </select>
                    </div>
                </td>
                <td width="33%" >
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Descuento</label>
                            <div class="input-group">
                                <input  :disabled="disabledEditar || disabledEditarTabla" type="input-right-addon"  v-model="parent_articulo.descuento"   name="articulos[{{parent_index}}][descuento]"  class="form-control debito" id="descuento">
                                <span class="input-group-addon">%</span>
                            </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="form-group" style="margin-bottom: 5px !important">
                        <label class="control-label">Cuenta</label>
                           <select  :disabled="disabledEditar || disabledEditarTabla" data-rule-required="true" data-placeholder="Seleccione" v-model="parent_articulo.cuenta_id" name="articulos[{{parent_index}}][cuenta_id]" class="form-control chosen-select"   >
                          	<option value="">Seleccione</option>
                           	<option value="{{cuenta.id}}"  v-for="cuenta in cuentas | orderBy 'nombre'" >{{cuenta.nombre}}</option>
                         </select>

                    </div>
                </td>
            
            </tr>
            
        </tbody>

    </table>
   
    
</template>