<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control chosen-select" id="cliente_id" data-rule-required="true" v-on:change="clienteChange(datosFactura.cliente)" v-model="datosFactura.cliente" :disabled="vista =='refacturar_ver'">
          <option value="">Seleccione</option>
          <?php foreach($clientes as $cliente) {?>
          <option value="<?php echo $cliente->id?>"><?php echo $cliente->nombre?></option>
          <?php }?>
        </select>
        <label id="cliente_id-error" class="error" for="cliente_id"></label>
    </div>
<?php //dd($info['info']->centros_contable_id);?>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="termino_pago">Términos de pago <span required="" aria-required="true">*</span></label>
      <select name="campo[termino_pago]" class="form-control " id="termino_pago" v-model="datosFactura.termino_pago" data-rule-required="true">
        <option value="">Seleccione</option>
        <?php foreach($terminos_pagos as $termino) {?>
        <option  value="<?php echo $termino->etiqueta?>"><?php echo $termino->valor?></option>
        <?php }?>
      </select>
        <label id="termino_pago-error" class="error" for="termino_pago"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="input-left-addon" disabled name="campo[saldo]" value="{{datosFactura.saldo | currency}}"   class="form-control debito"  id="campo[saldo]">
          </div>
          <label class="label-danger-text">Saldo pendiente acumulado</label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
          <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled value="{{datosFactura.credito | currency}}"    name="campo[lcredito]" value="" class="form-control debito" id="campo[lcredito]">
          </div>
          <label class="label-success-text">Crédito a favor</label>
      </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_desde">Fecha de emisión <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
            <input  type="text" name="campo[fecha_desde]" class="form-control"  id="fecha_desde" data-rule-required="true" value="" v-model="datosFactura.fecha_desde" disabled>
      </div>
      <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="fecha_hasta">Válido hasta <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></span>
          <input type="text" name="campo[fecha_hasta]" class="form-control"  id="fecha_hasta" data-rule-required="true"  value="" v-model="datosFactura.fecha_hasta">
    </div>
    <label id="fecha_hasta-error" class="error" for="fecha_hasta"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label>Vendedor <span required="" aria-required="true">*</span></label>
      <select name="campo[creado_por]" class="form-control chosen-select" id="vendedor" data-rule-required="true"  v-model="datosFactura.vendedor">
        <option value="">Seleccione</option>
        <?php foreach($vendedores as $vendedor) {?>
        <option  value="<?php echo $vendedor->id?>"><?php echo $vendedor->nombre." ".$vendedor->apellido?></option>
        <?php }?>
      </select>
       <label id="vendedor-error" class="error" for="vendedor"></label>
      </div>

      <!--<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 hidden">
        <label>Lista de precio <span required="" aria-required="true">*</span></label>
        <select name="campo[item_precio_id]" class="form-control lista_precio" id="item_precio_id" data-rule-required="true" v-model="datosFactura.lista_precio">
          <option value="">Seleccione</option>
          <?php foreach($precios as $precio) {?>
          <option  value="<?php echo $precio->id?>"><?php echo $precio->nombre?></option>
          <?php }?>
        </select>
        <label id="item_precio_id-error" class="error" for="item_precio_id"></label>
      </div>-->
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="centro_contable_id">Centro Contable <span required="" aria-required="true">*</span></label>
        <select name="campo[centro_contable_id]" class="form-control" id="centro_contable_id" data-rule-required="true" v-model="datosFactura.centro_contable_id">
            <option value="">Seleccione</option>
            <?php foreach($centros_contables as $centro) {?>
                <option value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
            <?php }?>
        </select>
        <label id="centro_contable_id-error" class="error" for="centro_contable_id"></label>
    </div>

</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Cuenta a acreditar <span required="" aria-required="true">*</span></label>
        <select class="form-control"  id="cuenta" name="campo[cuenta]" v-model="datosFactura.cuenta" data-rule-required="true"  >
            <option value="">Seleccione</option>
            <option value="{{cuenta.id}}" v-for="cuenta in cuentas">{{cuenta.codigo +' '+ cuenta.nombre}}</option>
        </select>

        <label id="cuenta-error" class="error" for="cuenta"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Estado <span required="" aria-required="true">*</span></label>
        <!-- <select name="campo[estado]" v-model="datosFactura.estado" class="form-control" id="estado" data-rule-required="true">
        <option value="">Seleccione</option>
        <?php foreach($etapas as $etapa) {?>
        <option value="<?php echo $etapa->etiqueta?>"   :disabled="<?php echo $etapa->etiqueta; ?>=='anulada'" ><?php echo $etapa->valor?></option>
        <?php }?>
      </select>-->
        <select class="form-control"  id="estado" name="campo[estado]" v-model="datosFactura.estado"  data-rule-required="true"  :disabled="datosFactura.factura_id != '' && etapa.etiqueta == 'anulada' &&  (datosFactura.estado =='por_cobrar' || datosFactura.estado =='cobrado_parcial' || datosFactura.estado =='cobrado_completo')">
            <option value="">Seleccione</option>
            <option value="{{etapa.etiqueta}}" v-for="etapa in etapas">{{{etapa.valor}}}</option>
        </select>

        <label id="estado-error" class="error" for="estado"></label>
    </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">

  </div>
<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

   <tabla-facturas-compras :lista="factura_compras"></tabla-facturas-compras>


</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
    <div class="input-group"><span class="input-group-addon">$</span>
        <input type="text" id="total_pago" name="campo[total]" v-model="total_facturas" class="form-control" disabled/>
      </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
    <label class="label-info-text">Total</label>
    <label id="totals-error" class="error"></label>
  </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
    <label>Observaciones </label>
    <textarea id="comentario" name="campo[comentario]" v-model="datosFactura.comentario" class="form-control"></textarea>
  </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('facturas/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">

        <input type="hidden"  id="factura_id" v-model="datosFactura.factura_id"  name="campo[factura_id]"  value="{{datosFactura.factura_id}}">
        <input type="hidden" name="campo[formulario]" value="{{datosFactura.formulario}}">
     <button id="guardarBtn"  class="btn btn-primary btn-block" :disabled="datosFactura.estado==='cobrado_parcial' || datosFactura.estado==='cobrado_completo'" @click.stop="guardar">Guardar</button>
    </div>
</div>
<!-- Termina campos de Busqueda -->

