<template id="datos_factura">

    <div class="ibox" id="datos_factura">
        <div class="ibox-title border-bottom">
            <h5>Datos de la factura</h5>
            <div class="ibox-tools">
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </div>
        </div>
        <div style="display: block; border: 0px" class="ibox-content m-b-sm">

            <div class="row">
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Cliente <span class="required">*</span></label>
                    <select class="form-control" data-rule-required="true"  name="campo[cliente_id]" id="cliente_id" v-model="factura.cliente_id" :disabled="factura.cliente_id !=''">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in factura.clienteOptions | orderBy 'nombre'">{{option.nombre}}</option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>T&eacute;rminos de pago <span class="required">*</span></label>
                    <select data-rule-required="true"  class="form-control" id="termino_pago" name="campo[termino_pago]" v-model="factura.termino_pago_id" :disabled="factura.uuid_venta !=='' && factura.factura_id == ''">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in factura.terminosPagoOptions">{{option.nombre}}</option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>&nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" disabled name="campo[saldo]" class="form-control debito" value="{{factura.saldo_pendiente | currency ''}}" >
                    </div>
                    <label class="label-danger-text">Saldo por cobrar</label>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>&nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="input-left-addon" disabled name="campo[lcredito]" class="form-control debito" value="{{factura.credito_favor | currency ''}}" >
                    </div>
                    <label class="label-success-text">Cr&eacute;dito a favor</label>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Fecha de emisi&oacute;n <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                        <input type="text" name="campo[fecha_desde]" class="form-control"  id="fecha_desde" v-datepicker="factura.fecha_desde" v-model="factura.fecha_desde" data-rule-required="true" value="">
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>V&aacute;lido hasta <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></span>
                        <input type="text" name="campo[fecha_hasta]" class="form-control" id="fecha_hasta" v-datepicker="factura.fecha_hasta" v-model="factura.fecha_hasta" value="" >
                    </div>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Vendedor <span class="required">*</span></label>
                    <select class="form-control" data-rule-required="true"  id="vendedor" name="campo[creado_por]" v-model="factura.vendedor_id" :disabled="factura.uuid_venta !=='' && factura.factura_id == ''">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in factura.vendedoresOptions | orderBy 'nombre'" selected="{{option.id == factura.vendedorId}}">{{{option.nombre}}}</option>
                    </select>
                </div>

                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Lista de precio <span class="required">*</span></label>
                    <select class="form-control" id="lista_precio_id" name="campo[item_precio_id]" @change="cambiarListaPrecio" v-model="factura.lista_precio_id" data-rule-required="true" >
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in factura.listaPreciosOptions | orderBy 'nombre'" selected="{{option.id == factura.precioId}}">{{{option.nombre}}}</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Centro Contable <span class="required">*</span></label>
                    <select class="form-control" data-rule-required="true"  id="centro_contable_id" name="campo[centro_contable_id]" v-model="factura.centro_contable_id" :disabled="factura.uuid_venta !=='' && factura.factura_id == ''">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in factura.centrosContablesOptions | orderBy 'nombre'">{{{option.nombre}}}</option>
                    </select>
                </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Centro de facturaci&oacute;n <span class="required">*</span></label>
                    <select  data-rule-required="true"  class="form-control" id="centro_facturacion_id" name="campo[centro_facturacion_id]" v-model="factura.centro_facturacion_id" :disabled="factura.centrosFacturacionOptions.length<0 || factura.id !==''">
                            <option value="">Seleccione</option>
                        <option v-for="option in factura.centrosFacturacionOptions | orderBy 'nombre'" :value="option.id" v-text="option.nombre"></option>
                    </select>
                </div>
               <!-- <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Despacho desde bodega <span class="required">*</span></label>
                    <select class="form-control" id="bodega_id" name="campo[bodega_id]" v-model="factura.bodega_id" :disabled="factura.uuid_venta !=='' && factura.factura_id == ''">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in factura.bodegasOptions | orderBy 'nombre'">{{{option.nombre}}}</option>
                    </select>
                </div> -->
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Estado <span class="required">*</span></label>
                    <select class="form-control" id="estado_id" name="campo[estado]" v-model="factura.estado_id" data-rule-required="true" :disabled="factura.id =='' || factura.estado_id=='anulada' || factura.estado_id=='cobrado_parcial' || factura.estado_id=='cobrado_completo'">
                        <option value="">Seleccione</option>
                        <option value="{{option.id}}" v-for="option in getEstados" >{{{option.nombre}}}</option>
                    </select>
                </div>
            </div>

            <div class="row">
              <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                  <label>Referencia <span required="" aria-required="true">*</span></label>
                  <input type="text" name="campo[referencia]" class="form-control" id="referencia" data-rule-required="true" v-model="factura.referencia">
              </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <label>Observaciones </label>
                    <textarea id="comentario" name="campo[comentario]" v-model="factura.comentario" class="form-control"></textarea>
                </div>
            </div>

        </div>
    </div>

    <items_factura :factura="factura" v-ref:tabla></items_factura>

    <input type="hidden" v-model="factura.fac_facturable_id" name="fac_facturable_id" >
    <input type="hidden" v-model="factura.factura_id" name="campo[factura_id]" />

</template>
<?php ?>
