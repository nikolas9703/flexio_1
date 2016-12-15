<?php
    $info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="proveedor_id">Proveedor <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[proveedor_id]" class="form-control chosen-select" id="proveedor_id" data-rule-required="true" ng-change="ngChanged.proveedor(datosFactura.proveedor)" ng-model="datosFactura.proveedor" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || (uuid_tipo !=='' && vista == 'crear')">
            <option value="">Seleccione</option>
            <?php foreach($proveedores as $proveedor) {?>
            <option value="<?php echo $proveedor->id?>"><?php echo $proveedor->nombre?></option>
            <?php }?>
        </select>
        <label id="proveedor_id-error" class="error" for="proveedor_id"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="bodega_id">Recibir en bodega <span required="" aria-required="true">*</span></label>
        <select name="campo[bodega_id]" class="form-control chosen-select" id="bodega_id" data-rule-required="true" ng-model="datosFactura.bodega" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((uuid_tipo !=='' && tipo == 'Ordenes_orm') && vista == 'crear')">
            <option value="">Seleccione</option>
            <?php foreach($bodegas as $bodega) {?>
            <option value="<?php echo $bodega->id?>"><?php echo $bodega->nombre?></option>
            <?php }?>
        </select>
        <label id="bodega_id-error" class="error" for="bodega_id"></label>
    </div>
    
    <?php //dd($info['info']->centros_contable_id);?>
    
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosFactura.saldo" name="campo[saldo]" value="P. D." class="form-control debito"  id="campo[saldo]">
        </div>
        <label class="label-danger-text">Saldo pendiente acumulado</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled ng-model="datosFactura.credito" name="campo[lcredito]" value="P. D." class="form-control debito" id="campo[lcredito]">
        </div>
        <label class="label-success-text">Crédito a favor</label>
    </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="factura_proveedor">No. de factura de proveedor <span required="" aria-required="true">*</span></label>
        <input type="text" name="campo[factura_proveedor]" class="form-control"  id="factura_proveedor" data-rule-required="true"  value="<?php if(isset($info['cotizacion'])){ echo $info['cotizacion']['factura_proveedor']; }?>" ng-model="datosFactura.factura_proveedor">
        <label id="factura_proveedor-error" class="error" for="factura_proveedor"></label>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_desde">Fecha de emisión <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
            <input type="text" name="campo[fecha_desde]" class="form-control"  id="fecha_desde" data-rule-required="true" value="<?php if(isset($info['cotizacion'])){ echo $info['cotizacion']['fecha_desde']; }?>" ng-model="datosFactura.fecha_desde">
        </div>
        <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
    </div>
    <?php //dd($info['info']->centros_contable_id);?>
    

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="centro_contable_id">Centro contable <span required="" aria-required="true">*</span></label>
        <select name="campo[centro_contable_id]" class="form-control chosen-select" id="centro_contable_id" data-rule-required="true" ng-model="datosFactura.centro_contable" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || (uuid_tipo !=='' && vista == 'crear')">
            <option value="">Seleccione</option>
            <?php foreach($centros_contables as $centro) {?>
                <option value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
            <?php }?>
        </select>
        <label id="centro_contable_id-error" class="error" for="centro_contable_id"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="termino_pago">Términos de pago <span required="" aria-required="true">*</span></label>
        <select name="campo[termino_pago]" class="form-control chosen-select" id="termino_pago" ng-model="datosFactura.termino_pago" data-rule-required="true">
            <option value="">Seleccione</option>
            <?php foreach($terminos_pagos as $termino) {?>
                <option  value="<?php echo $termino->etiqueta?>"><?php echo $termino->valor?></option>
            <?php }?>
        </select>
        <label id="termino_pago-error" class="error" for="termino_pago"></label>
    </div>



</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Comprador <span required="" aria-required="true">*</span></label>
        <select name="campo[creado_por]" class="form-control chosen-select" id="comprador" data-rule-required="true" ng-model="datosFactura.comprador" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((uuid_tipo !=='' && tipo == 'Ordenes_orm') && vista == 'crear')">
            <option value="">Seleccione</option>
            <?php foreach($compradores as $comprador) {?>
                <option  value="<?php echo $comprador->id?>"><?php echo $comprador->nombre." ".$comprador->apellido?></option>
            <?php }?>
        </select>
        <label id="comprador-error" class="error" for="comprador"></label>
    </div>


    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"></div>
        
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div style="display:table-cell;">
        <table class="table table-noline tabla-dinamica" id="tableFacturasItems">
            <thead>
                <tr>
                  <th width="12%">Categor&iacute;a</th>
                  <th width="12%">Item</th>
                  <th width="4%">Cantidad</th>
                  <th width="8%">Unidad</th>
                  <th width="6%">Precio Unidad</th>
                  <th width="6%">Impuesto</th>
                  <th width="6%">Descuento</th>
                  <th width="12%">Cuenta</th>
                  <th width="8%">Precio Total</th>
                  <th width="6%" colspan="2">&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                <tr id="items{{$index}}" class="item-listing" ng-repeat="articulo in articulos track by $index">
                    <td>
                        <select data-placeholder="Seleccione" id="categoria_id{{$index}}" name="items[{{$index}}][categoria_id]" class="chosen-select form-control categoria" data-rule-required="true" ng-model="articulo.categoria" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((uuid_tipo !=='' && tipo == 'Ordenes_orm') && vista == 'crear')" ng-change="ngChanged.itemCategoria(articulo.categoria, $index)">
                            <option value="">Seleccione</option>
                            <?php foreach($categorias as $categoria):?>
                            <option value="<?php echo $categoria->id;?>"><?php echo $categoria->nombre;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" id="factura_item_id{{$index}}" name="items[{{$index}}][factura_item_id]" ng-disable="factura_id===''" value="{{articulo.factura_item_id}}">
                        <select data-placeholder="Seleccione" id="item_id{{$index}}" name="items[{{$index}}][item_id]" class="chosen-select form-control item-change item" ng-model="articulo.item" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || (disabled && vista == 'crear')" data-rule-required="true" ng-change="ngChanged.itemItem(articulo.item, $index)">
                            <option value="">Seleccione</option>
                            <option value="{{item_cat.id}}" ng-repeat="item_cat in articulo.items track by $index">{{item_cat.nombre}}</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" id="cantidad{{$index}}" name="items[{{$index}}][cantidad]" class="form-control cantidad-item cantidad" value="1" data-rule-required="true" data-inputmask="'mask':'9{1,4}','greedy':false"  ng-model="articulo.cantidad" ng-blur="ngBlur.itemCantidad(articulo.cantidad,$index)" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((disabled || articulo.item =='') && vista == 'crear')">
                    </td>
                    <td>
                        <select id="unidad_id{{$index}}"  name="items[{{$index}}][unidad_id]" class="form-control unidad-item unidad" data-rule-required="true" ng-change="ngChanged.itemUnidad(articulo.unidad,$index)"  ng-model="articulo.unidad" ng-disabled="disabled || articulo.item ==''">
                            <option value="">Seleccione</option>
                            <option value="{{unidad.id}}" ng-repeat="unidad in articulo.unidades track by $index">{{unidad.nombre}}</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" id="precio_unidad{{$index}}" name="items[{{$index}}][precio_unidad]" class="form-control precio_unidad" data-inputmask="'mask':'9{1,8}[.*{1,2}]','greedy':false" ng-model="articulo.precio_unidad" ng-blur="ngBlur.itemPrecioUnidad(articulo.precio_unidad, index)" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((disabled || articulo.item =='') && vista == 'crear')">
                        </div>
                    </td>
                    <td>
                        <select id="impuesto_id{{$index}}" name="items[{{$index}}][impuesto_id]" ng-model="articulo.impuesto" class="form-control item-impuesto impuesto" data-rule-required="true" ng-change="ngChanged.itemImpuesto(articulo.impuesto, $index)" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((disabled || articulo.item =='') && vista == 'crear')">
                            <option value="">Seleccione</option>    
                            <?php
                            foreach($impuestos as $impuesto) {?>
                            <option value="<?php echo $impuesto->uuid_impuesto?>" data-impuesto="<?php echo $impuesto->impuesto?>"><?php echo $impuesto->nombre?></option>
                            <?php }?>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" id="descuento{{$index}}" ng-model="articulo.descuento" name="items[{{$index}}][descuento]" class="form-control item-descuento descuento" value="0.00" placeholder="0.00" data-rule-required="true" data-inputmask="'mask':'9{1,2}[.*{1,2}]','greedy':false" data-rule-range="[0,100]" ng-blur="ngBlur.itemDescuento(articulo.descuento, $index)" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((disabled || articulo.item =='') && vista == 'crear')">
                            <span class="input-group-addon">%</span>
                        </div>
                    </td>
                    <td>
                        <select id="cuenta_id{{$index}}" name="items[{{$index}}][cuenta_id]" class="form-control chosen-select item-cuenta cuenta" data-placeholder="Seleccione" data-rule-required="true" ng-model="articulo.cuenta" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || ((disabled || articulo.item =='') && vista == 'crear')">
                        <option value="">Seleccione</option>
                        <?php foreach($cuenta_gasto as $cuenta) {?>
                        <option value="<?php echo $cuenta->id?>"><?php echo $cuenta->nombre?></option>
                        <?php }?>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="input-left-addon" id="total{{$index}}" name="items[{{$index}}][total]" class="form-control total" placeholder="0.00" disabled ng-model="articulo.total">
                            <input type="hidden" id="subtotal{{$index}}" name="items[{{$index}}][subtotal]" class="form-control subtotal" value="{{articulo.subtotal}}">
                            <input type="hidden" id="descuentos{{$index}}" name="items[{{$index}}][descuentos]" class="form-control descuentos" value="{{articulo.descuentos}}">
                            <input type="hidden" id="impuestos{{$index}}" name="items[{{$index}}][impuestos]" class="form-control impuestos" value="{{articulo.impuestos}}">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-default btn-block" ng-click="ngClick.addRow($index)" data-rule-required="true" agrupador="items" aria-required="true" ng-show="$index === 0"><i class="fa fa-plus" ng-disabled="(vista == 'editar' && uuid_tipo != '0') || (disabled && vista == 'crear')"></i></button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" ng-click="$index === 0 ?'':ngClick.deleteRow(articulo)" ng-disabled="disabled"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>

            </tbody>
            <tfoot>
              <tr>
                <td colspan="8"></td>
                <td  class="sum-border"> <span>Subtotal: </span><span ng-bind="totales.subtotal | currency" id="tsubtotal" class="sum-total"></span></td>
                <td><input type="hidden" name="campo[subtotal]" id="hsubtotal" value="{{totales.subtotal}}"></td>
              </tr>
              <tr>
                <td colspan="8"></td>
                <td  class="sum-border"> <span>Descuentos: </span><span ng-bind="totales.descuentos | currency" id="tsubtotal" class="sum-total"></span></td>
                <td><input type="hidden" name="campo[descuento]" id="hdescuento" value="{{totales.descuentos}}"></td>
              </tr>
              <tr>
                <td colspan="8"></td>
                <td class="sum-border"><span>Impuestos:</span> <span ng-bind="totales.impuesto | currency" id="timpuesto"  class="sum-total"></span></td>
                <td><input type="hidden" name="campo[impuestos]" id="himpuesto" value="{{totales.impuesto}}"></td>
              </tr>
              <tr>
                <td colspan="8"></td>
                <td class="sum-border"><span>Total: </span> <span ng-bind="totales.total | currency" id="ttotal" class="sum-total"></span></td>
                <td><input type="hidden" name="campo[total]" id="htotal" value="{{totales.total}}"></td>
              </tr>
              <tr>
                <td colspan="9"><div id="tablaError"></div></td>
                <td></td>
              </tr>
            </tfoot>
        </table>

    </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
        <label>Comentarios </label>
        <textarea id="comentario" name="campo[comentario]" ng-model="datosFactura.comentario" class="form-control"><?php if(isset($info['cotizacion'])){ echo $info['cotizacion']['comentario']; }?></textarea>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Estado <span required="" aria-required="true">*</span></label>
        <select name="campo[estado]" ng-model="datosFactura.estado" class="form-control" id="estado" data-rule-required="true" ng-disabled="(vista == 'editar' && (datosFactura.estado == 'pagada_parcial' || datosFactura.estado == 'pagada_completa' || datosFactura.estado == 'anulada')) || (vista == 'crear')">
            <option value="">Seleccione</option>
            <?php foreach($etapas as $etapa) {?>
            <?php if($etapa->etiqueta == "por_aprobar" || $etapa->etiqueta == "por_pagar" || $etapa->etiqueta == "anulada"):?>
            <option value="<?php echo $etapa->etiqueta?>"><?php echo $etapa->valor?></option>
            <?php endif;?>
            <?php }?>
        </select>
        <label id="estado-error" class="error" for="estado"></label>
    </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('facturas_compras/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">

        <input type="hidden"  id="factura_id" name="campo[factura_id]" value="<?php echo isset($info["factura_id"]) ? $info["factura_id"] : ''?>" ng-model="factura_id">

        <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar" ng-disabled="(vista == 'editar' && (datosFactura.estado == 'pagada_parcial' || datosFactura.estado == 'pagada_completa'))"/>
    </div>
</div>
