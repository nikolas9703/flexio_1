        <span class="input-group-addon">$</span><input type="text" id="precio_total{{$index}}" name="items[{{$index}}][monto_pagado]" class="form-control moneda"  ng-model="factura.precio_total" data-rule-number="true" ng-focus="focusPago($index)" ng-blur="cambiarCantidad($index,factura.precio_total)"  placeholder="0.00"/></div>
=======
        <span class="input-group-addon">$</span><input type="text" id="precio_total{{$index}}" name="items[{{$index}}][monto_pagado]" class="form-control moneda"  ng-model="factura.precio_total" ng-focus="focusPago($index)" ng-blur="cambiarCantidad($index,factura.precio_total)" data-rule-number="true"  placeholder="0.00"/></div>

<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="fecha_desde">Fecha de cobro <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="text" name="campo[fecha_pago]" class="form-control"  id="fecha_pago" data-rule-required="true" value="" ng-model="datosCobro.fecha_pago" data-rule-required="true">
    </div>
    <label id="fecha_pago-error" class="error" for="fecha_pago"></label>
  </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control chosen-select" id="cliente_id" data-rule-required="true" ng-change="clienteChange(datosCobro.clienteActual)" ng-model="datosCobro.clienteActual" ng-disabled="disableSelected !==''">
          <option value="">Seleccione</option>
          <?php foreach($clientes as $cliente) {?>
          <option value="<?php echo $cliente->id?>"><?php echo $cliente->nombre?></option>
          <?php }?>
        </select>
        <label id="cliente_id-error" class="error" for="cliente_id"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="input-left-addon" disabled ng-model="datosCobro.saldo" name="campo[saldo]" value="" class="form-control debito"  id="campo[saldo]">
          </div>
          <label class="label-danger-text">Saldo por cobrar</label>
      </div>

      <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
          <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosCobro.credito" name="campo[lcredito]" value="" class="form-control debito" id="campo[lcredito]">
          </div>
          <label class="label-success-text">Crédito a favor</label>
      </div>

</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <!-- <div style="display:table-cell;"> -->
   <table class="table" id="facturaItems">
    <thead>
      <tr>
      <th width="14%">No. Factura</th>
      <th width="14%">Fecha de Emisión</th>
      <th width="14%">Fecha de finalización</th>
      <th width="14%">Monto</th>
      <th width="14%">Pagado</th>
      <th width="14%">Saldo por cobrar</th>
      <th width="">Pago</th>
    </tr>
    </thead>
    <tbody>

    <tr id="items{{$index}}" class="item-listing" ng-repeat="factura in facturas track by $index">
    <td>
      <input type="hidden" id="factura_id{{$index}}" name="items[{{$index}}][factura_id]" ng-disable="" ng-model="factura.factura_id" value="{{factura.factura_id}}">
      <span ng-bind="factura.codigo"></span>
    </td >
    <td ng-bind="factura.fecha_emision"></td>
    <td ng-bind="factura.fecha_finalizacion"></td>
    <td ng-bind="factura.monto | currency"></td>
    <td ng-bind="factura.pagado | currency"></td>
    <td ng-bind="factura.saldo_pendiente | currency"></td>
    <td><div class="input-group">
        <span class="input-group-addon">$</span><input type="text" id="precio_total{{$index}}" name="items[{{$index}}][monto_pagado]" class="form-control moneda"  ng-model="factura.precio_total" ng-click="focusPago($index)" ng-change="cambiarCantidad($index,factura.precio_total)"  placeholder="0.00"/></div>
    </td>
    </tr>
    </tbody>

  </table>

<!-- </div> -->

</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
    <label>Monto</label>
  </div>
  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
    <div class="input-group">
        <span class="input-group-addon">$</span>
    <input type="text" id="monto" name="monto" ng-model="datosCobro.monto" class="form-control"  disabled/></div>
  </div>
  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
    <select name="campo[tipo_deposito]" id="tipo_deposito" ng-model="datosCobro.tipo_deposito" class="form-control" ng-change="depositoEn(datosCobro.tipo_deposito)" ng-disabled="disableCuenta">
      <?php foreach($tipo_cobro as $tipo){?>
      <option value="<?php echo $tipo->etiqueta?>"><?php echo $tipo->valor?></option>
      <?php }?>
    </select>
  </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">

      <select name="campo[depositable_id]" ng-model="datosCobro.depositable_id" class="form-control" id="cuenta" data-rule-required="true" ng-disabled="disableCuenta">
        <option value="">Seleccione</option>

        <option ng-repeat="option in depositable" value="{{option.id}}">{{option.nombre}}</option>

      </select>
      <label id="cuenta_id-error" class="error" for="cuenta_id"></label>
      </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 item-listing lists_opciones" id="opciones{{$index}}"  ng-repeat="pago in opcionPagos track by $index">
  <div class="lists_opciones" id="opcionesRow{{$index}}">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Método de Pago <span required="" aria-required="true">*</span></label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <select class="form-control" name="metodo_pago[{{$index}}][tipo_pago]" id="tipo_pago{{$index}}" ng-model="pago.tipo_pago" ng-change="selecionePago($index, pago.tipo_pago)" data-rule-required="true">
        <option value="">Seleccione</option>
        <?php foreach($tipo_pagos as $pago){?>
          <option value="<?php echo $pago['etiqueta'] ?>"><?php echo $pago['valor'] ?></option>
        <?php }?>
      </select>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Total Pagado <span required="" aria-required="true">*</span></label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
      <div class="input-group"><span class="input-group-addon">$</span>
          <input type="text" id="total_pagado{{$index}}" name="metodo_pago[{{$index}}][total_pagado]" ng-model="pago.total_pagado" class="form-control moneda" style="text-align:right" ng-change="sumaTotales($index)" data-rule-required="true"  placeholder="0.00"/>
        </div>
        <label id="total_pagado{{$index}}-error" class="error" for="total_pagado{{$index}}"></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <button type="button" class="btn btn-default btn-block" agrupador="opciones" ng-click="$index===0? addRow($index): deleteRow($index)" ng-show="cobrosHeader.tipo ==='factura'"><i class="{{pago.icon}}"></i></button>

        </div>
    </div>
  </div>
  </div>
  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='ach'" ng-class="pagoClass($index)">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Banco del Cliente</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <input type="text" name="metodo_pago[{{$index}}][nombre_banco_ach]" id="nombre_banco_ach{{$index}}" class="form-control" />
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de cuenta del Cliente</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
      <input type="text" name="metodo_pago[{{$index}}][cuenta_cliente]" id="cuenta_cliente{{$index}}" class="form-control" />
    </div>
  </div>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='cheque'" ng-class="pagoClass($index)">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número Cheque</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <input type="text" name="metodo_pago[{{$index}}][numero_cheque]" id="numero_cheque{{$index}}" class="form-control" />
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Nombre Banco</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
      <input type="text" name="metodo_pago[{{$index}}][nombre_banco_cheque]" id="nombre_banco_cheque{{$index}}" class="form-control" />
    </div>
  </div>

  <!--<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='tarjeta_de_credito'" ng-class="pagoClass($index)">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de tarjeta</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <input type="text" name="metodo_pago[{{$index}}][numero_tarjeta]" id="numero_tarjeta{{$index}}" class="form-control" />
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de recibo</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
      <input type="text" name="metodo_pago[{{$index}}][numero_recibo]" id="numero_recibo{{$index}}" class="form-control" />
    </div>
  </div>-->

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
    <div class="input-group"><span class="input-group-addon">$</span>
        <input type="text" id="total_pago" name="campo[monto_pagado]" ng-model="datosCobro.total_pago" class="form-control moneda" disabled/>
      </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
    <label class="label-info-text">Total</label>
    <label id="totals-error" class="error"></label>
  </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('cobros/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <input type="hidden" name="campo[id]" id="cobro_id" value="{{datosCobro.id}}" ng-disabled="datosCobro.id ===''"/>
     <input type="hidden" name="campo[formulario]" id="formulario" value="{{cobrosHeader.tipo}}"/>
     <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar"  />
    </div>
</div>
<!-- Termina campos de Busqueda -->