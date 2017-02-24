<template id="reporte_de_caja">
  <validator name="validar">
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-lg-3">
        <label for="caja_id">Nombre de la caja <span required="" aria-required="true">*</span></label>
        <div class="form-inline">
            <div class="form-group">
                <div class="input-group">
                  <select v-validate:reporte.nombre_de_caja="{required: { rule: true, initial: 'off' }}" name="cajaID" class="form-control" id="cajaID" v-model="reporte.id_caja">
                    <option value="">Seleccione</option>
                      <option  v-for="caja in cats.cajas" v-bind:value="caja.id">{{caja.nombre}}</option>
                  </select>
                </div>

            </div>
        </div>
    </div>
    <div class="form-group col-lg-3">
        <label for="centro_contable">Centro contable <span required="" aria-required="true">*</span></label>
        
            <div class="form-group">
                <div class="input-group" style="width: 100%;">
                  <input type="text" v-validate:reporte.centro_contable="{required: { rule: true, initial: 'off' }}" name="centro_contable" class="form-control" id="centro_contable" v-model="reporte.centro_contable" :disabled=true />
                </div>

            </div>
       
    </div>
    <div class="form-group col-lg-3">
        <label for="responsable_id">Responsable <span required="" aria-required="true">*</span></label>
        
            <div class="form-group">
                <div class="input-group" style="width: 100%;">
                  <input type="text" v-validate:reporte.responsable="{required: { rule: true, initial: 'off' }}" name="responsable_id" class="form-control" id="responsable_id" v-model="reporte.responsable" :disabled=true>
                </div>

            </div>
       
    </div>
    <div class="form-group col-lg-3">
      <label for="rango_de_fechas">
Rango de fechas<span required="" aria-required="true">*</span></label>

    <div class="form-group">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="text" v-datepicker="reporte.fecha_desde" id="fecha_desde" v-model="reporte.fecha_desde" name="fecha_desde" class="form-control" v-validate:reporte.fecha_desde="{required: { rule: true, initial: 'off' }}">
            <span class="input-group-addon">a</span>
            <input type="text" v-datepicker="reporte.fecha_hasta" id="fecha_hasta" v-model="reporte.fecha_hasta" name="fecha_hasta" class="form-control" v-validate:reporte.fecha_hasta="{required: { rule: true, initial: 'off' }}">
        </div>
    </div>
    </div>
    <div class="form-group col-lg-4">
    </div>

    <div class="form-group col-lg-4">
      </div>
</div>
<div class="col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;
      <div class="errors">
        <!--<p v-if="$validar.mes.required">Mes es requerido</p>
        <p v-if="$validar.year.required">A&ntilde;o es requerido</p>-->
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
