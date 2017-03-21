<template id="costo_por_centro_compras">
  <validator name="validar">
  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

      <div class="form-group col-lg-3">
        <label>Centro contable <span required="" aria-required="true">*</span></label>
        <select name="centro_contable_id" class="form-control " id="centro_contable_id" v-model="reporte.centro_contable_id" v-select2="reporte.centro_contable_id" :config="config.select2" v-validate:centro_contable_id="{required: { rule: true, initial: 'off' }}">
          <option value="todos">Todos</option>
          <option  v-for="centro in centros" v-bind:value="centro.id">{{centro.nombre}}</option>
        </select>
      </div>

      <div class="form-group col-lg-3">
        <label>Cuenta</label>
        <select name="cuenta_id" class="form-control " id="cuenta_id" v-model="reporte.cuenta_id" v-select2="reporte.cuenta_id" :config="config.select2">
          <option value="todos">Todos</option>
          <option  v-for="cuenta in cuentas" v-bind:value="cuenta.id">{{cuenta.codigo + ' ' +cuenta.nombre}}</option>
        </select>
      </div>

      <div class="form-group col-lg-3">
        <label>Categor&iacute;a(s) de item <span required="" aria-required="true">*</span></label>
        <select name="categoria_id" class="form-control " id="categoria_id" v-model="reporte.categoria_id" v-select2="reporte.categoria_id" :config="config.select2" v-validate:categoria_id="{required: { rule: true, initial: 'off' }}">
          <option value="todos">Todos</option>
          <option  v-for="categoria in categorias" v-bind:value="categoria.id">{{categoria.nombre}}</option>
        </select>
      </div>

      <div class="form-group col-lg-3">
        <label for="termino_pago">Rango de fechas <span required="" aria-required="true">*</span></label>
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
          <p v-if="$validar.centro_contable_id.required">Centro contable es requerido</p>
          <p v-if="$validar.categoria_id.required">Categoria es requerido</p>
          <p v-if="$validar.fecha_desde.required">fecha inicio es requerido</p>
          <p v-if="$validar.fecha_hasta.required">fecha fin es requerido</p>
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
