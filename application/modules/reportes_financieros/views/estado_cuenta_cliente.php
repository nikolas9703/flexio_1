<template id="form_estado_cuenta_cliente">
  <validator name="validar">
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select name="cliente" class="form-control " id="cliente" v-model="reporte.cliente" v-validate:cliente="{required: { rule: true, initial: 'off' }}" @change="clienteSeleccionado(reporte.cliente)">
          <option value="">Seleccione</option>
          <option  v-for="cliente in clientes" v-bind:value="cliente.id">{{cliente.nombre}}</option>
        </select>
    </div>
    <div class="form-group col-lg-3">
        <label for="centro_facturacion_id">Centro de facruraci&oacute;n </label>
        <select name="centro_facturacion_id" class="form-control " id="centro_facturacion_id" v-model="reporte.centro_facturacion_id">
          <option value="">Todos</option>
          <option  v-for="centro in centros_facturables" v-bind:value="centro.id">{{centro.nombre}}</option>
        </select>
    </div>
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
        <p v-if="$validar.cliente.required">Cliente requerido</p>
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
