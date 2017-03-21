
<template id="detalle_template">

    <div class="row">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Proveedor <span required="" aria-required="true">*</span></label>
            <select name="campo[proveedor]" id="proveedor_id" data-rule-required="true" aria-required="true" v-model="detalle.proveedor_id" v-select2="detalle.proveedor_id" :config="config.select2" :disabled="config.disableDetalle || config.isDisableByStatus || detalle.ordenes_multiple==1">
                <option value="">Seleccione</option>
                <option :value="proveedor.proveedor_id" v-for="proveedor in catalogos.proveedores">{{proveedor.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Términos de pago <span required="" aria-required="true">*</span></label>
            <select name="campo[termino_pago]" class="chosen" id="termino_pago" data-rule-required="true" aria-required="true" style="display: none;" v-model="detalle.terminos_pago" v-select2="detalle.terminos_pago" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="termino_pago.etiqueta" v-for="termino_pago in catalogos.terminos_pago">{{{termino_pago.valor}}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled class="form-control debito" value="{{detalle.saldo_proveedor | currency ''}}">
            </div>
            <label class="label-danger-text">Saldo por pagar</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled class="form-control debito" value="{{detalle.credito_proveedor | currency ''}}">
            </div>
            <label class="label-success-text">Crédito a favor</label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="factura_proveedor">No. de factura de proveedor <span required="" aria-required="true">*</span></label>
            <input type="text" name="campo[factura_proveedor]" class="form-control"  id="factura_proveedor" data-rule-required="true" v-model="detalle.nro_factura_proveedor" :disabled="config.disableDetalle">
            <label id="factura_proveedor-error" class="error" for="factura_proveedor"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_desde">Fecha de emisión <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                <input type="text" name="campo[fecha_desde]" class="form-control"  id="fecha_desde" aria-required="true" v-model="detalle.fecha" v-datepicker2="detalle.fecha" :config="config.datepicker2" :disabled="config.disableDetalle">
            </div>
            <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label>Creado por <span required="" aria-required="true">*</span></label>
            <select name="campo[creado_por]" data-placeholder="Seleccione" class="form-control" id="comprador" data-rule-required="true" v-model="detalle.creado_por" v-select2-catalog="detalle.creado_por" :config="select2_usuarios"  :disabled="true">
                <option value="">Seleccione</option>
                <option :value="usuario.id" v-for="usuario in catalogos.usuarios">{{usuario.nombre}}</option>
            </select>
            <label id="comprador-error" class="error" for="comprador"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Centro contable <span required="" aria-required="true">*</span></label>
            <select name="campo[centro]" class="chosen" id="centro" data-rule-required="true" aria-required="true" v-model="detalle.centro_contable_id" v-select2="detalle.centro_contable_id" :config="config.select2" :disabled="config.disableDetalle || detalle.ordenes_multiple==1">
                <option value="">Seleccione</option>
                <option :value="centro_contable.centro_contable_id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Referencia <span required="" aria-required="true">*</span></label>
            <input type="text" name="campo[referencia]" class="form-control"  id="referencia" data-rule-required="true" v-model="detalle.referencia" :disabled="config.disableDetalle">
            <label id="referencia-error" class="error" for="referencia"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Recibir en bodega <span required="" aria-required="true">*</span></label>
            <select name="campo[lugar]" class="chosen" id="lugar" data-rule-required="true" aria-required="true" v-model="detalle.recibir_en_id" v-select2-catalog="detalle.recibir_en_id" :config="select2_bodega" :disabled="config.disableDetalle || detalle.ordenes_multiple==1">
                <option value="">Seleccione</option>
                <option :value="bodega.bodega_id" v-for="bodega in catalogos.bodegas">{{bodega.nombre}}</option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" v-if="config.muestroRetenidoSubContrato"  >
            <label for="porcentaje">Porcentaje de retención  </label>


            <input type="text"  name="campo[porcentaje_retencion]" class="form-control"  id="porcentaje_retencion" data-rule-required="true" v-model="detalle.porcentaje_retencion" :disabled="config.disableDetallePorcentaje || config.disableDetalle"  >
            <label id="porcentaje_retencion-error" class="error" for="porcentaje_retencion"></label>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Estado <span required="" aria-required="true">*</span></label>
            <select name="campo[estado]" class="chosen" id="estado" data-rule-required="true" aria-required="true" v-model="detalle.estado" v-select2="detalle.estado" :config="config.select2" :disabled="config.disableDetalle || config.vista == 'crear' || tienePoliticas">
                <option value="">Seleccione</option>
                <option :value="estado.id" v-for="estado in catalogos.estados">{{{estado.valor}}}</option>
            </select>
        </div>

        <template v-for="(index, orden_id) in detalle.ordenes_id">
          <input type="hidden" name="ordenes[{{index}}][id]" :value="orden_id" />
        </template>
    </div>

</template>
