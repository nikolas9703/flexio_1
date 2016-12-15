<?php
    $info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_pago">Fecha de pago <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_pago]" class="form-control"  id="fecha_pago" data-rule-required="true" value="" ng-model="datosPago.fecha_pago" data-rule-required="true">
        </div>
        <label id="fecha_pago-error" class="error" for="fecha_pago"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="proveedor">Proveedor <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[proveedor]" class="form-control chosen-select" id="proveedor" data-rule-required="true" ng-change="proveedorChange(datosPago.proveedorActual)" ng-model="datosPago.proveedorActual" ng-disabled="disableSelected !==''">
          <option value="">Seleccione</option>
          <?php foreach($proveedores as $proveedor) {?>
          <option value="<?php echo $proveedor->id?>"><?php echo $proveedor->nombre?></option>
          <?php }?>
        </select>
        <label id="proveedor-error" class="error" for="proveedor"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosPago.saldo" name="campo[saldo]" value="" class="form-control debito"  id="campo[saldo]">
        </div>
        <label class="label-danger-text">Saldo pendiente acumulado</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosPago.credito" name="campo[lcredito]" value="" class="form-control debito" id="campo[lcredito]">
        </div>
        <label class="label-success-text">Crédito a favor</label>
    </div>

</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <!-- <div style="display:table-cell;"> -->
    <table class="table" id="facturaItems">
    <thead>
      <tr>
      <th width="14%">No. Documento</th>
      <th width="14%">Fecha de Emisión</th>
      <th width="14%">Fecha de finalización</th>
      <th width="14%">Monto</th>
      <th width="14%">Pagado</th>
      <th width="14%">Saldo Pendiente</th>
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
            <td ng-bind="factura.monto"></td>
            <td ng-bind="factura.pagado"></td>
            <td ng-bind="factura.saldo_pendiente"></td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" id="precio_total{{$index}}" name="items[{{$index}}][monto_pagado]" class="form-control precio_total"  ng-model="factura.precio_total" ng-click="focusPago($index)" ng-change="cambiarCantidad($index,factura.precio_total)" data-inputmask="'mask':'9{1,6}[.*{1,2}]'" placeholder="0.00"/>
                </div>
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
            <input type="text" id="monto" name="monto" ng-model="datosPago.monto" class="form-control"  disabled/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Pagar de Cuenta de Banco <span required="" aria-required="true">*</span></label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <select name="campo[cuenta_id]" ng-model="datosPago.cuenta_id" class="form-control" id="cuenta" data-rule-required="true" ng-disabled="disableCuenta">
            <option value="">Seleccione</option>
            <?php foreach($cuenta_bancos as $banco){?>
            <option value="<?php echo $banco->id?>"><?php echo $banco->codigo . ' | '.$banco->nombre ?></option>
            <?php }?>
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
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="input-group"><span class="input-group-addon">$</span>
                        <input type="text" id="total_pagado{{$index}}" name="metodo_pago[{{$index}}][total_pagado]" ng-model="pago.total_pagado" class="form-control" ng-change="sumaTotales($index)" data-rule-required="true" data-inputmask="'mask':'9{1,6}[.*{2}]'" placeholder="0.00"/>
                    </div>
                    <label id="total_pagado{{$index}}-error" class="error" for="total_pagado{{$index}}"></label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display: none;">
                    <button type="button" class="btn btn-default btn-block" agrupador="opciones" ng-click="$index===0? addRow($index): deleteRow($index)"><i class="{{pago.icon}}"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='ach'" ng-class="pagoClass($index)">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Banco del Cliente</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <select name="metodo_pago[{{$index}}][nombre_banco_ach]" id="nombre_banco_ach{{$index}}" class="form-control" >
                <option value=""></option>
                <?php foreach($bancos as $banco):?>
                <option value="<?php echo $banco->id?>"><?php echo $banco->nombre?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Número de cuenta del Proveedor</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
            <input type="text" name="metodo_pago[{{$index}}][cuenta_proveedor]" id="cuenta_proveedor{{$index}}" class="form-control" />
        </div>
    </div>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='cheque'" ng-class="pagoClass($index)">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Número Cheque</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <input type="text" name="metodo_pago[{{$index}}][numero_cheque]" id="numero_cheque{{$index}}" class="form-control" disabled=""/>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Nombre Banco</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
            <input type="text" name="metodo_pago[{{$index}}][nombre_banco_cheque]" id="nombre_banco_cheque{{$index}}" class="form-control" disabled=""/>
        </div>
    </div>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='tarjeta_de_credito'" ng-class="pagoClass($index)">
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
  </div>
  
  <!-- Forma de Pago Caja Menuda - Detalle -->
  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="pago.tipo_pago ==='caja_chica'" ng-class="pagoClass($index)">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Seleccione la caja</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
		 <select name="metodo_pago[{{$index}}][caja_id]" id="caja_id{{$index}}" class="form-control" >
         	<option value="">Seleccione</option>
         	<option ng-repeat="caja in cajasList track by $index" value="{{caja.id && '' || caja.id}}">{{caja.nombre && '' || caja.nombre}}</option>
         </select>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-6"></div>
  </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
    <div class="input-group"><span class="input-group-addon">$</span>
        <input type="text" id="total_pago" name="campo[total_pagado]" ng-model="datosPago.total_pago" class="form-control" disabled/>
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
      <a href="<?php echo base_url('pagos_contratos/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <input type="hidden" name="campo[id]" id="pago_id" value="{{datosPago.id}}" ng-disabled="datosPago.id ===''"/>
     <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar"  />
    </div>
</div>
<!-- Termina campos de Busqueda -->
