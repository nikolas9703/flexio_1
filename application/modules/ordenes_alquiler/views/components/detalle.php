

<template id="detalle_template">

    <div class="row">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Cliente {{isClientePotencial}}<span required="" aria-required="true">*</span></label>
            <select name="campo[cliente_id]" id="cliente_id" data-rule-required="true" aria-required="true" v-model="detalle.cliente_id" v-select2="detalle.cliente_id" :config="config.select2" :disabled="config.disableDetalle || empezable.type != ''">
                <option value="">Seleccione</option>
                <option :value="cliente.id" v-for="cliente in catalogos.clientes">{{cliente.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Términos de pago <span required="" aria-required="true">*</span></label>
            <select name="campo[termino_pago]" id="termino_pago" data-rule-required="true" aria-required="true" v-model="detalle.termino_pago" v-select2="detalle.termino_pago" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="termino_pago.etiqueta" v-for="termino_pago in catalogos.terminos_pago">{{{termino_pago.valor}}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled class="form-control debito" value="{{detalle.saldo_cliente | currency ''}}">
            </div>
            <label class="label-danger-text">Saldo por cobrar</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled class="form-control debito" value="{{detalle.credito_cliente | currency ''}}">
            </div>
            <label class="label-success-text">Crédito a favor</label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_desde">Fecha de emisión <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                <input type="text" name="campo[fecha_desde]" class="form-control"  id="fecha_desde" data-rule-required="true" v-model="detalle.fecha_desde" v-datepicker2="detalle.fecha_desde" :config="config.fecha_desde" :disabled="config.disableDetalle">
            </div>
            <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <label for="fecha_hasta">Válido hasta <span required="" aria-required="true">*</span></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></span>
                <input type="text" name="campo[fecha_hasta]" class="form-control"  id="fecha_hasta" data-rule-required="true" v-model="detalle.fecha_hasta" v-datepicker2="detalle.fecha_hasta" :config="config.fecha_hasta" :disabled="config.disableDetalle">
            </div>
            <label id="fecha_hasta-error" class="error" for="fecha_hasta"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Vendedor <span required="" aria-required="true">*</span></label>
            <select name="campo[creado_por]" class="" id="creado_por" data-rule-required="true" aria-required="true" v-model="detalle.creado_por" v-select2="detalle.creado_por" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="vendedor.id" v-for="vendedor in catalogos.vendedores">{{vendedor.nombre}}</option>
            </select>
        </div>

        <!-- Lista de Precio de Alquiler -->
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" ><!-- style='clear: both;' -->
            <label for="vendedor_id">Lista de precio de alquiler <span required="" aria-required="true">*</span></label>
            <select name="campo[precio_alquiler_id]" class="form-control" data-rule-required="true" v-model="detalle.precio_alquiler_id" disabled>
                <option value="">Seleccione</option>
                <option value="{{option.id}}" v-for="option in catalogos.precios_alquiler" track-by="$index">{{option.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 " style="clear: both;">
            <label>Centro contable <span required="" aria-required="true">*</span></label>
            <select name="campo[centro_contable_id]" class="" id="centro_contable_id" data-rule-required="true" aria-required="true" v-model="detalle.centro_contable_id" v-select2="detalle.centro_contable_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="centro_contable.centro_contable_id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Centro de facturaci&oacute;n </label>
            <select name="campo[centro_facturacion_id]" class="" id="centro_facturacion_id" v-model="detalle.centro_facturacion_id" v-select2="detalle.centro_facturacion_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="centro_facturacion.id" v-for="centro_facturacion in detalle.centros_facturacion">{{centro_facturacion.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 " v-show="false">
            <label>Despacho desde bodega <span required="" aria-required="true">*</span></label>
            <select class="" v-model="detalle.bodega_id" v-select2="detalle.bodega_id" :config="config.select2">
                <option value="">Seleccione</option>
                <option :value="bodega.bodega_id" v-for="bodega in catalogos.bodegas">{{bodega.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Estado <span required="" aria-required="true">*</span></label>
            <select name="campo[estado]" class="" id="estado" data-rule-required="true" aria-required="true" v-model="detalle.estado" v-select2="detalle.estado" :config="config.select2" :disabled="config.disableDetalle || config.vista == 'crear'">
                <option value="">Seleccione</option>
                <option :value="estado.etiqueta" v-for="estado in catalogos.estados">{{{estado.valor}}}</option>
            </select>
        </div>

    </div>

</template>
