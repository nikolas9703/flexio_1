
  <script id="tempcuentas-montos" type="x-template">
  <table class="table table-noline" id="montos_componente">
    <thead>
      <tr>
        <th width="25%">Cuenta</th>
        <th width="25%">Descripci&oacute;n</th>
        <th width="25%">Monto</th>
        <th width="2%"></th>
        <th width="20%"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in lista">
        <td><select class="form-control select2" name="components[{{$index}}][cuenta_id]" id="acomponents_cuenta_id{{$index}}" data-rule-required="true" v-model="item.cuenta_id">
          <option value="">Seleccione</option>
          <?php foreach($cuenta_ingreso as $ingreso){ ?>
            <option value="<?php echo $ingreso->id ?>"><?php echo $ingreso->codigo." ".$ingreso->nombre  ?></option>
          <?php } ?>
        </select></td>
        <td><input type="text" class="form-control" name="components[{{$index}}][descripcion]" id="acomponents_descripcion{{$index}}" data-rule-required="true" v-model="item.descripcion"></td>
        <td>
          <div class="input-group">
               <span class="input-group-addon">$</span>
               <input type="text" v-model="item.monto" name="components[{{$index}}][monto]" id="acomponents_contrato_monto{{$index}}" class="form-control" data-rule-required="true" data-rule-number="true">
          </div>
        </td>
        <td>
          <button type="button" class="btn btn-default btn-block" v-show="$index === 0"  v-on:click="addRow($event)" data-rule-required="true" agrupador="items" aria-required="true"><i class="fa fa-plus"></i></button>
          <button  type="button" v-show="$index !== 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="lista.length === 1 ?'':deleteRow(item)"><i class="fa fa-trash"></i></button>
        </td>
        <td></td>
      </tr>
    </tbody>
  </table>
</script>
