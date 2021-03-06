<?php
    $info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_abono">Fecha de abono <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_abono]" class="form-control"  id="fecha_abono" data-rule-required="true" value="" ng-model="datosAbono.fecha_abono" data-rule-required="true">
        </div>
        <label id="fecha_abono-error" class="error" for="fecha_abono"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="proveedor">Proveedores <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[proveedor]" class="form-control chosen-select" id="proveedor" data-rule-required="true" ng-change="proveedorChange(datosAbono.proveedorActual)" ng-model="datosAbono.proveedorActual" ng-disabled="disableSelected !==''">
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
            <input type="input-left-addon" disabled ng-model="datosAbono.saldo" name="campo[saldo]" value="" class="form-control debito"  id="campo[saldo]">
        </div>
        <label class="label-danger-text">Saldo por cobrar</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosAbono.credito" name="campo[lcredito]" value="" class="form-control debito" id="campo[lcredito]">
        </div>
        <label class="label-success-text">Crédito a favor</label>
    </div>

</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Monto a abonar</label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" id="monto" name="monto" ng-model="datosAbono.monto" ng-change="sumaTotales(0)" class="form-control" data-inputmask="'mask':'9{1,6}[.*{1,2}]'"/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Pagar de Cuenta de Banco <span required="" aria-required="true">*</span></label>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <select name="campo[cuenta_id]" ng-model="datosAbono.cuenta_id" class="form-control" id="cuenta" data-rule-required="true" ng-disabled="disableCuenta">
            <option value="">Seleccione</option>
            <?php foreach($cuenta_bancos as $banco){ ?>
            <!-- <option value="<?php echo $banco->id?>"><?php echo $banco->codigo . ' | '.$banco->nombre ?></option> -->
                <option value="<?php echo $banco->cuenta->id?>"><?php echo $banco->cuenta->codigo . ' | '.$banco->cuenta->nombre?></option>
            <?php }?>
        </select>
        <label id="cuenta_id-error" class="error" for="cuenta_id"></label>
    </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 item-listing lists_opciones" id="opciones{{$index}}"  ng-repeat="abono in opcionAbonos track by $index">
    <div class="lists_opciones" id="opcionesRow{{$index}}">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Método de Abono <span required="" aria-required="true">*</span></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <select class="form-control" name="metodo_abono[{{$index}}][tipo_abono]" id="tipo_abono{{$index}}" ng-model="abono.tipo_abono" ng-change="selecioneAbono($index, abono.tipo_abono)" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach($tipo_abonos as $abono){?>
                <option value="<?php echo $abono['etiqueta'] ?>"><?php echo $abono['valor'] ?></option>
                <?php }?>
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Total Abonado <span required="" aria-required="true">*</span></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="input-group"><span class="input-group-addon">$</span>
                        <input type="text" id="total_abonado{{$index}}" name="metodo_abono[{{$index}}][total_abonado]" ng-model="abono.total_abonado" class="form-control" ng-change="sumaTotales($index)" data-rule-required="true" data-inputmask="'mask':'9{1,6}[.*{2}]'" placeholder="0.00"/>
                    </div>
                    <label id="total_abonado{{$index}}-error" class="error" for="total_abonado{{$index}}"></label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display: none;">
                    <button type="button" class="btn btn-default btn-block" agrupador="opciones" ng-click="$index===0? addRow($index): deleteRow($index)"><i class="{{abono.icon}}"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="abono.tipo_abono ==='ach'" ng-class="abonoClass($index)">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Banco del Cliente</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <select name="metodo_abono[{{$index}}][nombre_banco_ach]" id="nombre_banco_ach{{$index}}" class="form-control" >
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
            <input type="text" name="metodo_abono[{{$index}}][cuenta_proveedor]" id="cuenta_proveedor{{$index}}" class="form-control" />
        </div>
    </div>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="abono.tipo_abono ==='cheque'" ng-class="abonoClass($index)">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Número Cheque</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <input type="text" name="metodo_abono[{{$index}}][numero_cheque]" id="numero_cheque{{$index}}" class="form-control" disabled=""/>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Nombre Banco</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
            <input type="text" name="metodo_abono[{{$index}}][nombre_banco_cheque]" id="nombre_banco_cheque{{$index}}" class="form-control" disabled=""/>
        </div>
    </div>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 no-left-padding" ng-show="abono.tipo_abono ==='tarjeta_de_credito'" ng-class="abonoClass($index)">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de tarjeta</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <input type="text" name="metodo_abono[{{$index}}][numero_tarjeta]" id="numero_tarjeta{{$index}}" class="form-control" />
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de recibo</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 last-input">
      <input type="text" name="metodo_abono[{{$index}}][numero_recibo]" id="numero_recibo{{$index}}" class="form-control" />
    </div>
  </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9">
    <div class="input-group"><span class="input-group-addon">$</span>
        <input type="text" id="total_abono" name="campo[total_abonado]" ng-model="datosAbono.total_abono" class="form-control" disabled/>
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
      <a href="<?php echo base_url('proveedores/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <input type="hidden" name="campo[id]" id="abono_id" value="{{datosAbono.id}}" ng-disabled="datosAbono.id ===''"/>
     <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar"  />
    </div>
</div>
<!-- Termina campos de Busqueda -->
