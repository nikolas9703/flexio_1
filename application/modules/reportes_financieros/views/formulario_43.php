<template id="formulario_43">
  <validator name="validar">
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-lg-4">
        <label for="cliente_id">Informe al <span required="" aria-required="true">*</span></label>
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
    <div class="form-group col-lg-4">
    </div>

    <div class="form-group col-lg-4">
      </div>
</div>
<div class="col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;
      <div class="errors">
        <p v-if="$validar.mes.required">Mes es requerido</p>
        <p v-if="$validar.year.required">A&ntilde;o es requerido</p>
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
