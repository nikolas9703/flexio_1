<template id="estado_cuenta_proveedor">
  <validator name="validar">
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-lg-3">
        <label for="provedor_id">Proveedor <span required="" aria-required="true">*</span></label>
        <select name="proveedor" class="form-control " id="provedor" v-model="reporte.proveedor" v-validate:proveedor="{required: { rule: true, initial: 'off' }}">
          <option value="">Seleccione</option>
          <option v-for="provedor in proveedores" v-bind:value="provedor.id">{{provedor.nombre}}</option>
        </select>

    </div>
<?php //dd($info['info']->centros_contable_id);?>
    <div class="form-group col-lg-3">
      <label for="termino_pago">
Rango de fechas<span required="" aria-required="true">*</span></label>
<div class="form-inline">
    <div class="form-group">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="text" v-datepicker="reporte.fecha_desde" id="fecha_desde" v-model="reporte.fecha_desde" name="fecha_desde" class="form-control" v-validate:fecha_desde="{required: { rule: true, initial: 'off' }}">

            <span class="input-group-addon">a</span>
            <input type="text" v-datepicker="reporte.fecha_hasta" id="fecha_hasta" v-model="reporte.fecha_hasta" name="fecha_hasta" class="form-control" v-validate:fecha_hasta="{required: { rule: true, initial: 'off' }}">
        </div>
    </div>
</div>
    </div>

    <div class="form-group col-lg-6"></div>

</div>

<div class="col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;
      <div class="errors">
        <p v-if="$validar.proveedor.required">Proveedor requerido</p>
        <p v-if="$validar.fecha_desde.required">fecha es requerido</p>
        <p v-if="$validar.fecha_hasta.required">fecha es requerido</p>
      </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" @click="limpiar()"/>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden" name="tipo" id="tipo" v-model="reporte.tipo" value="{{reporte.tipo}}">
        <input type="button" id="generarBtn" class="btn btn-primary btn-block" value="Generar" @click="generar_reporte(reporte)" :disabled="!$validar.valid"/>
    </div>
</div>
  </validator>
</template>
