
<template id="detalle_template">

    <div class="row">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Fecha <span required="" aria-required="true">*</span></label>
            <input type="text" name="campo[fecha_creacion]" class="form-control" data-rule-required="true" id="fecha_creacion" aria-required="true" v-model="detalle.fecha_creacion" v-datepicker2="detalle.fecha_creacion" :config="config.datepicker2" :disabled="config.disableFechaCreacion">
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Centro contable <span required="" aria-required="true">*</span></label>
            <select name="campo[uuid_centro]" class="" id="uuid_centro" data-rule-required="true" aria-required="true" v-model="detalle.uuid_centro" v-select2="detalle.uuid_centro" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="centro_contable.id" v-for="centro_contable in catalogos.centros_contables">{{centro_contable.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Recibir en <span required="" aria-required="true">*</span></label>
            <select name="campo[uuid_lugar]" class="" id="uuid_lugar" data-rule-required="true" aria-required="true" v-model="detalle.uuid_lugar" v-select2="detalle.uuid_lugar" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="bodega.id" v-for="bodega in catalogos.bodegas">{{bodega.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Creado por <span required="" aria-required="true">*</span></label>
            <select name="campo[creado_por]" class="" id="creado_por" data-rule-required="true" aria-required="true" v-model="detalle.creado_por" v-select2="detalle.creado_por" :config="config.select2" :disabled="true">
                <option value="">Seleccione</option>
                <option :value="comprador.id" v-for="comprador in catalogos.compradores">{{comprador.nombre}}</option>
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Referencia </label>
            <input type="text" name="campo[referencia]" class="form-control" id="campo[referencia]" v-model="detalle.referencia" :disabled="config.disableDetalle">
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Estado <span required="" aria-required="true">*</span></label>
            <select name="campo[id_estado]" class="" id="estado" data-rule-required="true" aria-required="true" v-model="detalle.estado" v-select2="detalle.estado" :config="config.select2" :disabled="config.disableDetalle || config.vista == 'crear' || tienePoliticas">
                <option value="">Seleccione</option>
                <option :value="estado.id_cat" v-for="estado in catalogos.estados">{{{estado.etiqueta}}}</option>
            </select>
        </div>

    </div>

</template>
