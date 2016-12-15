<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select name="campo[cliente_id]" class="form-control select2" id="cliente_id" data-rule-required="true" v-model="datosDevolucion.cliente_id" :disabled="disableCliente">
          <option value="">Seleccione</option>
          <?php foreach($clientes as $cliente) {?>
          <option value="<?php echo $cliente->id?>"><?php echo $cliente->nombre?></option>
          <?php }?>
        </select>
        <label id="cliente_id-error" class="error" for="cliente_id"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="fecha_desde">Fecha de factura <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="text" name="campo[fecha_factura]" class="form-control"  id="fecha_desde" data-rule-required="true"  v-model="datosDevolucion.fecha_factura" :disabled="disableDevolucion">
    </div>
    <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="input-left-addon" disabled name="campo[saldo]" value="{{datosDevolucion.saldo | currency ''}}" class="form-control debito"  id="campo[saldo]">
          </div>
          <label class="label-danger-text">Saldo por cobrar</label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
          <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled value="{{datosDevolucion.credito | currency ''}}" name="campo[credito]"  class="form-control debito" id="campo[lcredito]">
          </div>
          <label class="label-success-text">Crédito a favor</label>
      </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_hasta">Fecha de devolución <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_devolucion]" class="form-control"  id="fecha_hasta" data-rule-required="true" v-model="datosDevolucion.fecha_devolucion">
      </div>
      <label id="fecha_hasta-error" class="error" for="fecha_hasta"></label>
    </div>
<?php //dd($info['info']->centros_contable_id);?>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="centro_contable_id">Centro Contable <span required="" aria-required="true">*</span></label>
      <select name="campo[centro_contable_id]" class="form-control" id="centro_contable_id" data-rule-required="true" v-model="datosDevolucion.centro_contable_id" :disabled="disableDevolucion">
        <option value="">Seleccione</option>
        <?php foreach($centros_contables as $centro) {?>
        <option value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
        <?php }?>
      </select>
    <label id="centro_contable_id-error" class="error" for="centro_contable_id"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label for="bodega_id">Devoluci&oacute;n a bodega <span required="" aria-required="true">*</span></label>
      <select name="campo[bodega_id]" class="form-control" id="bodega_id" data-rule-required="true" v-model="datosDevolucion.bodega_id" :disabled="disableDevolucion">
        <option value="">Seleccione</option>
        <?php foreach($bodegas as $bodega) {?>
        <option value="<?php echo $bodega->id?>"><?php echo $bodega->nombre?></option>
        <?php }?>
      </select>
    <label id="bodega_id-error" class="error" for="bodega_id"></label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Vendedor <span required="" aria-required="true">*</span></label>
        <select name="campo[created_by]" class="form-control select2" id="vendedor" data-rule-required="true" v-model="datosDevolucion.created_by">
          <option value="">Seleccione</option>
          <?php foreach($vendedores as $vendedor) {?>
          <option  value="<?php echo $vendedor->id?>"><?php echo $vendedor->nombre." ".$vendedor->apellido?></option>
          <?php }?>
        </select>
        <label id="vendedor-error" class="error" for="vendedor"></label>
      </div>

</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label for="razon">Raz&oacute;n <span required="" aria-required="true">*</span></label>
      <select name="campo[razon]" class="form-control" id="razon" data-rule-required="true" v-model="datosDevolucion.razon">
        <option value="">Seleccione</option>
        <?php foreach($razones as $razon) {?>
        <option value="<?php echo $razon->etiqueta?>"><?php echo $razon->valor?></option>
        <?php }?>
      </select>
    <label id="razon-error" class="error" for="razon"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-12 col-md-9 col-lg-9">

  </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
 <devolucion-productos :productos="articulos" :boton.sync="botonDisabled" :error="tablaError" :vista="vista"></devolucion-productos>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
    <label>Comentarios </label>
    <textarea id="comentario" name="campo[comentario]" v-model="datosDevolucion.comentario" class="form-control"></textarea>
  </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label>Estado <span required="" aria-required="true">*</span></label>
      <select name="campo[estado]" v-model="datosDevolucion.estado" class="form-control" id="estado" data-rule-required="true">
        <option value="">Seleccione</option>
        <?php foreach($etapas as $etapa) {?>
        <option value="<?php echo $etapa->etiqueta?>"><?php echo $etapa->valor?></option>
        <?php }?>
      </select>
      <label id="estado-error" class="error" for="estado"></label>
      </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8"><label class="label-warning" v-text="mensajeError"></label></div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('devoluciones/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden"  id="id" name="campo[factura_id]" value="{{datosDevolucion.factura_id}}">
        <input type="hidden"  id="id" name="campo[id]" value="{{datosDevolucion.id}}">
        <input type="hidden" name="campo[formulario]" value="{{devolucionHeader.tipo}}">
      <button id="guardarBtn"  class="btn btn-primary btn-block" :disabled="botonDisabled"  v-on:click="guardar()">Guardar</button>
    </div>
</div>
<!-- Termina campos de Busqueda -->
