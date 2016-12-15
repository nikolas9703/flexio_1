<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control chosen-select" id="cliente_id" data-rule-required="true"  v-model="datosFactura.cliente_id" :disabled="disabledFactura">
          <option value="">Seleccione</option>
          <?php foreach($clientes as $cliente) {?>
          <option value="<?php echo $cliente->id?>"><?php echo $cliente->nombre?></option>
          <?php }?>
        </select>
        <label id="cliente_id-error" class="error" for="cliente_id"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="monto_factura">monto de Factura <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon">$</span>
          <input type="input" disabled name="campo[monto_factura]" v-model="datosFactura.total" class="form-control debito"  id="campo[monto_factura]">
    </div>
        <label id="termino_pago-error" class="error" for="termino_pago"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="input-left-addon" disabled name="campo[saldo]" value="{{datosFactura.cliente.saldo_pendiente | currency ''}}" class="form-control debito"  id="campo[saldo]">
          </div>
          <label class="label-danger-text">Saldo por cobrar</label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
          <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled name="campo[lcredito]" value="{{datosFactura.cliente.credito | currency ''}}" class="form-control debito" id="campo[lcredito]">
          </div>
          <label class="label-success-text">Crédito a favor</label>
      </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_desde">Fecha de Factura <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_factura]" class="form-control"  id="fecha_desde" data-rule-required="true" value="" v-model="datosFactura.fecha_desde" disabled>
      </div>
      <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="fecha">Fecha de nota de cr&eacute;dito <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="text" name="campo[fecha]" class="form-control"  id="fecha_hasta" data-rule-required="true"  value="" v-model="datos.fecha">
    </div>
    <label id="fecha-error" class="error" for="fecha"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label for="centro_contable_id">Centro Contable <span required="" aria-required="true">*</span></label>
      <select name="campo[centro_contable_id]" class="form-control" id="centro_contable_id" data-rule-required="true" v-model="datosFactura.centro_contable_id" :disabled="disabledFactura">
        <option value="">Seleccione</option>
        <?php foreach($centros_contables as $centro) {?>
        <option value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
        <?php }?>
      </select>
      <label id="item_precio_id-error" class="error" for="item_precio_id"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label>Vendedor <span required="" aria-required="true">*</span></label>
      <select name="campo[creado_por]" class="form-control" id="vendedor" data-rule-required="true"  v-model="datosFactura.created_by" :disabled="disabledFactura">
        <option value="">Seleccione</option>
        <?php foreach($vendedores as $vendedor) {?>
        <option  value="<?php echo $vendedor->id?>"><?php echo $vendedor->nombre." ".$vendedor->apellido?></option>
        <?php }?>
      </select>
      <label id="vendedor-error" class="error" for="vendedor"></label>
      </div>
</div>
<!-- <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <label for="narracion">Narraci&oacute;n <span required="" aria-required="true">*</span></label>
      <input type="text" name="campo[narracion]" value="" class="form-control" data-rule-required="true" id="campo[narracion]"  v-model="datos.narracion" />
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 "><div class="checkbox m-r-xs">
        <input type="checkbox" name="campo[incluir]" value="" class="chekbox-incluir" id="campo[incluir]" v-model="incluir"/>
<label class="checkbox" for="campo[incluir]">Incluir narración a la descripción de la nota cr&eacute;dito </label></div></div>
    <label id="narracion-error" class="error" for="narracion"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
</div> -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <!-- componente de vue-->
   <nota-credito-items :rows.sync="filas" :error.sync="tablaError" :boton.sync="botonDisabled" :items.sync="itemsFactura"></nota-credito-items>
   <!-- componente de vue-->
   <div id="tablaError" class="error" v-text="tablaError"></div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
    <label>Observaciones <span required="" aria-required="true">*</span></label>
    <textarea id="comentario" name="campo[comentario]"  class="form-control" data-rule-required="true" :disabled="disabledComentario"></textarea>
  </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label>Estado <span required="" aria-required="true">*</span></label>
      <select name="campo[estado]" v-model="datos.estado" class="form-control" id="estado" data-rule-required="true" :disabled="estadoDisable">
        <option value="">Seleccione</option>
        <?php foreach($etapas as $etapa) {?>
        <option value="<?php echo $etapa->etiqueta?>"><?php echo $etapa->valor?></option>
        <?php }?>
      </select>
      <label id="estado-error" class="error" for="estado"></label>
      </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('notas_creditos/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden"  id="factura_id"  name="campo[factura_id]"  value="{{datosFactura.id}}">
        <input type="hidden"  id="centro_facturacion_id"  name="campo[centro_facturacion_id]"  value="{{datosFactura.centro_facturacion_id}}">
        <input type="hidden"  id="id"  name="campo[id]"  value="{{datos.id}}">
     <button id="guardarBtn"  class="btn btn-primary btn-block" :disabled="botonDisabled" @click.stop="guardar">Guardar</button>
    </div>
</div>
<!-- Termina campos de Busqueda -->
