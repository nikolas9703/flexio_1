<?php
    $info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_pago">Fecha de cheque <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_pago]" class="form-control"  id="fecha_cheque" data-rule-required="true" value="" ng-model="datosCheque.fecha_pago" ng-disabled="disabledEditar" data-rule-required="true">
        </div>
        <label id="fecha_pago-error" class="error" for="fecha_pago"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="proveedor">Proveedor <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[proveedor]" class="form-control chosen-select" id="proveedor" data-rule-required="true" ng-change="proveedorChange(datosCheque.proveedorActual)" ng-model="datosCheque.proveedorActual" ng-disabled="disableSelected !==''">
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
            <input type="input-left-addon" disabled ng-model="datosCheque.saldo" name="campo[saldo]" value="" class="form-control debito"  id="campo[saldo]">
        </div>
        <label class="label-danger-text">Saldo por pagar</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosCheque.credito" name="campo[lcredito]" value="" class="form-control debito" id="campo[lcredito]">
        </div>
        <label class="label-success-text">Crédito</label>
    </div>

</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <!-- <div style="display:table-cell;"> -->
    <table class="table" id="facturaItems">
    <thead>
      <tr>
      <th width="30%">No. Pago</th>
      <th width="30%">Fecha de Emisión</th>
      <th width="40%">Monto</th>
    </tr>
    </thead>
    <tbody>
        <tr id="items{{$index}}" class="item-listing" ng-repeat="factura in facturas track by $index">
            <td>
                <input type="hidden" id="pago_id{{$index}}" name="campo[pago_id]" ng-disable="" ng-model="factura.factura_id" value="{{factura.factura_id}}">
                <span ng-bind="factura.codigo"></span>
            </td >
            <td ng-bind="factura.fecha_emision"></td>
            <td ng-bind="factura.pagado"></td>
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
            <input type="text" id="monto" name="monto" ng-model="datosCheque.monto_pagado" class="form-control"  disabled/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Pagar de Chequera <span required="" aria-required="true">*</span></label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <select name="campo[chequera_id]" ng-model="datosCheque.chequera_id" class="form-control" id="chequera_id" data-rule-required="true" ng-disabled="disableChequera_id || disabledEditar" ng-change="proximoCheque(datosCheque.chequera_id)">
            <option value="">Seleccione</option>
            <?php foreach($chequeras as $chequera){?>
            <option value="<?php echo bin2hex($chequera->uuid_chequera); ?>"><?php echo $chequera->nombre ?></option>
            <?php }?>
        </select>
        <label id="cuenta_id-error" class="error" for="cuenta_id"></label>
    </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 item-listing lists_opciones" id="opciones{{$index}}"  ng-repeat="cheque in opcionCheques track by $index">

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label>Método de Pago <span required="" aria-required="true">*</span></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <select required="" class="form-control" name="metodo_pago[{{$index}}][tipo_pago]" id="tipo_pago{{$index}}" ng-model="cheque.tipo_cheque" ng-change="selecionePago($index, cheque.tipo_cheque)" disabled="disabled" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach($tipo_pagos as $pago){?>
                <option value="<?php echo $pago['etiqueta'] ?>"><?php echo $pago['valor'] ?></option>
                <?php }?>
            </select>
        </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Total Pagado<span required="" aria-required="true">*</span></label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="input-group"><span class="input-group-addon">$</span>
                    <input type="text" readonly="" id="total_pagado{{$index}}" name="campo[monto]" ng-model="datosCheque.monto_pagado" class="form-control" ng-change="sumaTotales($index)" data-inputmask="'mask':'9{1,6}[.*{2}]'" placeholder="0.00"/>
                </div>
                <label class="label-info-text">Total</label>
                <label id="totals-error" class="error"></label>
        </div>
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-class="pagoClass($index)">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Número de Cheque</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <input type="text" name="campo[numero]" id="numero_cheque" ng-model="datosCheque.numero_cheque" class="form-control" readonly=""/>
        </div>
    </div>



</div>



<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('cheques/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <input type="hidden" name="campo[id]" id="pago_id" value="{{datosCheque.id}}" ng-disabled="datosCheque.id ===''"/>
     <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar"  ng-disabled="disabledEditar"/>
    </div>
</div>
<!-- Termina campos de Busqueda -->
