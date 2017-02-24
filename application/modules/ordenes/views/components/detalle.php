
<template id="detalle_template">

    <div class="row">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Proveedor <span required="" aria-required="true">*</span></label>
            <select name="campo[proveedor]" class="chosen"  id="proveedor_id" data-rule-required="true" aria-required="true" v-select2="detalle.proveedor_id" :config="config.select2" :disabled="config.disableProveedor">
                <option value="">Seleccione</option>
                <option :value="proveedor.uuid_proveedor" v-for="proveedor in catalogos.proveedores">{{proveedor.nombre}}</option>
            </select>
        </div>
         <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Términos de pago <span required="" aria-required="true">*</span></label>
            <select name="campo[termino_pago]" class="chosen" id="termino_pago" data-rule-required="true" aria-required="true" v-select2="detalle.terminos_pago" :config="config.select2" :disabled="config.disableProveedor">
                <option value="">Seleccione</option>
                <option :value="termino_pago.etiqueta" v-for="termino_pago in catalogos.terminos_pago">{{{termino_pago.valor}}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled class="form-control debito" value="{{detalle.saldo | currency ''}}">
            </div>
            <label class="label-danger-text">Saldo por pagar</label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label></label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled class="form-control debito" value="{{detalle.credito | currency ''}}">
            </div>
            <label class="label-success-text">Crédito a favor</label>
        </div>

         <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Fecha de emisión <span required="" aria-required="true">*</span></label>
            <!--<input type="text" name="campo[fecha_creacion]" class="form-control" data-rule-required="true" id="fecha_creacion" aria-required="true" v-model="detalle.fecha" v-datepicker2="detalle.fecha" :config="config.datepicker2" :disabled="config.disableFecha">-->

               <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                <input type="text" name="campo[fecha_creacion]" class="form-control"  id="fecha_creacion" aria-required="true" v-model="detalle.fecha" v-datepicker2="detalle.fecha"  :config="config.datepicker2" :disabled="config.disableFecha">
            </div>
        </div>
         <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Válido hasta <span required="" aria-required="true">*</span></label>
             <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                <input type="text" name="campo[valido_hasta]" class="form-control"  id="valido_hasta" aria-required="true" v-model="detalle.valido_hasta" v-datepicker2="detalle.fecha" :config="config.datepicker2" :disabled="config.disableFecha">
            </div>
            <!--<input type="text" name="campo[valido_hasta]" class="form-control" data-rule-required="true" id="valido_hasta" aria-required="true" v-model="detalle.valido_hasta" v-datepicker2="detalle.fecha" :config="config.datepicker2" :disabled="config.disableFecha">-->
        </div>




            <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
            <label>Creado por <span required="" aria-required="true">*</span></label>
            <select name="campo[creado_por]" class="form-control" id="comprador" data-rule-required="true" v-select2="detalle.creado_por" :config="config.select2" :disabled="true">
                <option value="">Seleccione</option>
                <option :value="usuario.id" v-for="usuario in catalogos.usuarios">{{usuario.nombre}}</option>
            </select>
            <label id="comprador-error" class="error" for="comprador"></label>
        </div>




               <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Centro contable <span required="" aria-required="true">*</span></label>
            <select name="campo[centro]" class="chosen" id="centro" data-rule-required="true" aria-required="true" v-select2="detalle.centro_contable_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="centro_contable.id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Referencia </label>
            <input type="text" name="campo[referencia]" class="form-control" id="campo[referencia]" v-model="detalle.referencia" :disabled="config.disableDetalle">
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Recibir en bodega <span required="" aria-required="true">*</span></label>
            <select name="campo[lugar]" class="chosen" id="lugar" data-rule-required="true" aria-required="true" v-select2="detalle.recibir_en_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="bodega.id" v-for="bodega in catalogos.bodegas">{{bodega.nombre}}</option>
            </select>
        </div>




        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Estado <span required="" aria-required="true">*</span></label>
            <select name="campo[estado]" class="chosen" id="estado" data-rule-required="true" aria-required="true" v-select2="detalle.estado" :config="config.select2" :disabled="config.disableDetalle || config.vista == 'crear' || tienePoliticas ">
                <option value="">Seleccione</option>
                <option :value="estado.id_cat" v-for="estado in catalogos.estados">{{{estado.etiqueta}}}</option>
            </select>
        </div>






    </div>

</template>
