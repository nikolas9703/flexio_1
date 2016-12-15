<template id="balance_situacion">
  <validator name="validar">
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-lg-4">
        <label for="cliente_id">Balance al <span required="" aria-required="true">*</span></label>
        <div class="form-inline">
            <div class="form-group">
                <div class="input-group">
                  <select v-validate:mes="{required: { rule: true, initial: 'off' }}" name="mes" class="form-control" id="mes" v-model="reporte.mes">
                    <option value="">Seleccione</option>
                      <option  v-for="mes in meses" v-bind:value="mes.valor">{{mes.etiqueta}}</option>
                  </select>
                    <span class="input-group-addon"></span>
                    <select v-validate:year="{required: { rule: true, initial: 'off' }}" name="year" class="form-control " id="year" v-model ="reporte.year">
                      <option value="">Seleccione</option>
                      <option  v-for="year in years" v-bind:value="year.id">{{year.valor}}</option>
                    </select>
                </div>

            </div>
        </div>
    </div>
<?php //dd($info['info']->centros_contable_id);?>
    <div class="form-group col-lg-4">
      <label for="termino_pago">
Periodos a comparar <span required="" aria-required="true">*</span></label>
      <select name="periodo" class="form-control " id="periodo" v-model="reporte.periodo" v-validate:periodo="{required: { rule: true, initial: 'off' }}">
        <option value="">Seleccione</option>
        <option  v-for="periodo in periodo_comparar" v-bind:value="periodo">{{periodo}}</option>
      </select>
    </div>

    <div class="form-group col-lg-4">
        <label>Rango del periodo <span required="" aria-required="true">*</span></label>
        <select name="rango" class="form-control " id="rango" v-model="reporte.rango" v-validate:rango="{required: { rule: true, initial: 'off' }}">
          <option value="">Seleccione</option>
          <option  v-for="rango in rango_perido" v-bind:value="rango.etiqueta">{{rango.valor}}</option>
        </select>
      </div>
</div>
<div class="col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;
      <div class="errors">
        <p v-if="$validar.mes.required">Mes es requerido</p>
        <p v-if="$validar.year.required">A&ntilde;o es requerido</p>
        <p v-if="$validar.periodo.required">Periodo es requerido</p>
        <p v-if="$validar.rango.required">Rango es requerido</p>
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
