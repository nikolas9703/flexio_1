<?php $info = !empty($info) ? $info : array();
//dd($info);
?>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <label for="numero_contrato">Numero de Contrato <span required="" aria-required="true">*</span></label>
      <input type="text" disabled name="campo[codigo]"  class="form-control"  id="campo[codigo]" value="<?php echo empty($info)?$codigo: $info['codigo']?>">
      <label id="numero_contrato-error" class="error" for="numero_contrato"></label>
  </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control select2" id="cliente_id" data-rule-required="true" :disabled="desabilitado(vista)">
          <option value="">Seleccione</option>
          <?php foreach($clientes as $cliente) {?>
          <option <?php if(!empty($info)) {  if($info['cliente_id'] ==$cliente->id){ echo 'selected';}} ?> value="<?php echo $cliente->id?>"><?php echo $cliente->nombre?></option>
          <?php }?>
        </select>
        <label id="cliente_id-error" class="error" for="cliente_id"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
      <label for="fecha_inicio">Fecha de inicio <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
          <input type="text" name="campo[fecha_inicio]" class="form-control"  id="fecha_inicio" data-rule-required="true" value="<?php echo empty($info)?'': $info['fecha_inicio']?>" :disabled="desabilitado(vista)">
    </div>
    <label id="fecha_inicio-error" class="error" for="fecha_inicio"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label for="fecha_final">Fecha de fin <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></span>
            <input type="text" name="campo[fecha_final]" class="form-control"  id="fecha_final" data-rule-required="true" value="<?php echo empty($info)?'': $info['fecha_final']?>" :disabled="desabilitado(vista)">
      </div>
      <label id="fecha_fin-error" class="error" for="fecha_final"></label>
      </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <label for="referencia">Nombre de referencia <span required="" aria-required="true">*</span></label>
      <input type="text"  name="campo[referencia]"  class="form-control"  id="referencia" data-rule-required="true" v-model="campo.referencia" :disabled="desabilitado(vista)">
      <label id="referencia-error" class="error" for="referencia"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <label for="centro_id">Centro Contable <span required="" aria-required="true">*</span></label>
    <select data-placeholder="Seleccione" name="campo[centro_id]" class="form-control select2" id="centro_id" data-rule-required="true" v-model="campo.centro_id" :disabled="desabilitado(vista)">
      <option value="">Seleccione</option>
      <?php foreach($centros_contables as $centro) {?>
      <option value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
      <?php }?>
    </select>
  <label id="centro_id-error" class="error" for="centro_id"></label>
  </div>
  <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">

  </div>

</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <table class="table table-noline" id="prueba">
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
      <tr v-for="item in tablaDatos">
        <td><select class="form-control select2" name="items[{{$index}}][cuenta_id]" id="items_cuenta_id{{$index}}" data-rule-required="true" v-model="item.cuenta_id" :disabled="desabilitado(vista)">
          <option value="">Seleccione</option>
          <?php foreach($cuenta_ingreso as $ingreso){ ?>
            <option value="<?php echo $ingreso->id ?>"><?php echo $ingreso->codigo." ".$ingreso->nombre  ?></option>
          <?php } ?>
        </select></td>
        <td><input type="text" class="form-control" name="items[{{$index}}][descripcion]" id="items_descripcion{{$index}}" data-rule-required="true" v-model="item.descripcion" :disabled="desabilitado(vista)"></td>
        <td>
          <div class="input-group">
               <span class="input-group-addon">$</span>
               <input type="text" id="monto_{{$index}}" v-model="item.monto" name="items[{{$index}}][monto]" id="items_contrato_monto{{$index}}" class="form-control" data-rule-required="true" data-rule-number="true" :disabled="desabilitado(vista)">
          </div>
        </td>
        <td>
          <button type="button" class="btn btn-default btn-block" v-show="$index === 0"  v-on:click="addRow($event)" data-rule-required="true" agrupador="items" aria-required="true" :class="ocultarBoton(vista)"><i class="fa fa-plus"></i></button>
          <button  type="button" v-show="$index !== 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="tablaDatos.length === 1 ?'':deleteRow(item)" :class="ocultarBoton(vista)"><i class="fa fa-trash"></i></button>
        </td>
        <td></td>
      </tr>
    </tbody>
  </table>
<div id="tablaError"></div>
</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" v-show="adenda > 0">
  <div class="form-group col-md-3 col-lg-3  col-md-offset-6 col-lg-offset-6">
    <label for="centro_contable_id">Monto de adendas</label>
    <div class="input-group">
         <span class="input-group-addon">$</span>
         <input type="text" disabled id="monto_adenda" v-model="monto_adenda" class="form-control">
    </div>
  </div>
  <div class="col-lg-3 col-md-3"></div>
</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-md-3 col-lg-3  col-md-offset-6 col-lg-offset-6">
    <label for="centro_contable_id">{{(vista==='ver' ||vista==='agregar_adenda')?'Monto del contrato actual (sin ITBMS):':'Monto del contrato (sin ITBMS)'}}</label>
    <div class="input-group">
         <span class="input-group-addon">$</span>
         <input type="text" :disabled="disabledMonto" id="monto_contrato" v-model="monto_contrato" name="campo[monto_contrato]"  class="form-control">
    </div>
  </div>
  <div class="col-lg-3 col-md-3"></div>
</div>



<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><span>{{(vista==='ver' ||vista==='agregar_adenda')?'Abono del monto Original:':'Anticipo:'}}</span></div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <div class="input-group">
         <span class="input-group-addon">$</span>
         <input type="text" id="abono_monto" v-model="campo.abono.monto" name="abono[monto]"  @blur="abono_monto(campo.abono.monto)" class="form-control" data-rule-number="true" :disabled="desabilitado(vista)">
    </div>
  </div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <div class="input-group">
         <input type="text" id=abono_porcentaje"" v-model="campo.abono.porcentaje" @blur="abono_porcentaje(campo.abono.porcentaje)" name="abono[porcentaje]"  class="form-control" data-rule-number="true" :disabled="desabilitado(vista)">
         <span class="input-group-addon">%</span>
    </div>
  </div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <select class="form-control select2" id="abono_cuenta" name="abono[cuenta_id]" data-rule-required="true" v-model="campo.abono.cuenta_id" :disabled="desabilitado(vista)">
      <option value="">Seleccione</option>
      <?php foreach($cuentas as $cuenta){ ?>
        <option value="<?php echo $cuenta->id ?>"><?php echo $cuenta->codigo." ".$cuenta->nombre  ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><span>{{(vista==='ver' ||vista==='agregar_adenda')?'Retenido del monto Original:':'Retenido:'}}</span></div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <div class="input-group">
         <span class="input-group-addon">$</span>
         <input type="text" id="retenido_monto" v-model="campo.retenido.monto" name="retenido[monto]" @blur="retenido_monto(campo.retenido.monto)"  class="form-control" data-rule-number="true" :disabled="desabilitado(vista)">
    </div>
  </div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <div class="input-group">
         <input type="text" id="retenido_porcentaje" v-model="campo.retenido.porcentaje" name="retenido[porcentaje]" @blur="retenido_porcentaje(campo.retenido.porcentaje)"  class="form-control" data-rule-number="true" :disabled="desabilitado(vista)">
         <span class="input-group-addon">%</span>
    </div>
  </div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <select class="form-control select2" id="retenido_cuenta" name="retenido[cuenta_id]" data-rule-required="true" v-model="campo.retenido.cuenta_id" :disabled="desabilitado(vista)">
      <option value="">Seleccione</option>
      <?php foreach($cuentas as $cuenta){ ?>
        <option value="<?php echo $cuenta->id ?>"><?php echo $cuenta->codigo." ".$cuenta->nombre  ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
  <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
    <a href="<?php echo base_url('contratos/listar');?>" class="btn btn-default btn-block" :class="ocultarBoton(vista)" id="cancelarFormBtn">Cancelar </a>
  </div>
  <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
    <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" :class="ocultarBoton(vista)" :disabled="validate_montos" value="Guardar" v-on:click="guardar()"/>
  </div>
</div>
