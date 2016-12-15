<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
      <select id="cliente_id" data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control select2" v-select3="formulario.cliente_id" data-rule-required="true" v-model="formulario.cliente_id" :disabled="campoDisabled.clienteDisabled || disabledEditar">
          <option value="">Seleccione</option>
          <option value="{{cliente.id}}" v-for="cliente in catClientes.catalogo.clientes | orderBy 'nombre'">{{cliente.nombre}}</option>

      </select>
      <label id="cliente_id-error" class="error" for="cliente_id"></label>
  </div>
  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="termino_pago_id">T&eacute;rminos de pago <span required="" aria-required="true">*</span></label>
      <select id="termino_pago_id" data-placeholder="Seleccione" name="campo[termino_pago]" class="form-control" data-rule-required="true" v-model="formulario.termino_pago" :disabled="disabledEditar">
          <option value="">Seleccione</option>
          <option :value="termino_pago.etiqueta" v-for="termino_pago in catalogoFormulario.terminos_pagos | orderBy 'id'">{{termino_pago.valor}}</option>
      </select>
      <label id="termino_pago_id-error" class="error" for="termino_pago_id"></label>
  </div>


  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
      <div class="input-group">
          <span class="input-group-addon">$</span>
          <input type="input-left-addon" disabled=""  v-model="formulario.saldo_pendiente" class="form-control debito">
      </div>
      <label class="label-danger-text">Saldo por cobrar</label>
  </div>

  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
      <div class="input-group">
          <span class="input-group-addon">$</span>
          <input type="input-left-addon" disabled="" v-model="formulario.credito_favor" class="form-control credito">
      </div>
      <label class="label-success-text">Cr&eacute;dito a favor</label>
  </div>

  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label>Fecha de emisi&oacute;n <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="input-left-addon" name="campo[fecha_desde]" id="fecha_desde" class="form-control" data-rule-required="true" v-model="formulario.fecha_desde" v-datepicker2="formulario.fecha_desde" :config="configDatepicker.fecha_desde">
      </div>
      <label id="fecha_desde-error" class="error" for="fecha_desde"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
      <label for="fecha_hasta">V&aacute;lido hasta <span required="" aria-required="true">*</span></label>
      <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="input-left-addon" name="campo[fecha_hasta]" id="fecha_hasta" class="form-control" data-rule-required="true" v-model="formulario.fecha_hasta" v-datepicker2="formulario.fecha_hasta" :config="configDatepicker.fecha_hasta">
      </div>
      <label id="fecha_hasta-error" class="error" for="fecha_hasta"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="creado_por">Vendedor <span required="" aria-required="true">*</span></label>
      <select id="creado_por" data-placeholder="Seleccione" name="campo[creado_por]" class="form-control select2" data-rule-required="true" v-model="formulario.creado_por" :disabled="disabledEditar" v-select3="formulario.creado_por">
          <option value="">Seleccione</option>
          <option value="{{vendedor.id}}" v-for="vendedor in catalogoFormulario.vendedores | orderBy 'nombre' 'apellido'">{{vendedor.nombre +' '+ vendedor.apellido}}</option>
      </select>
      <label id="creado_por-error" class="error" for="creado_por"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" ><!-- style='clear: both;' -->
      <label for="vendedor_id">Lista de precio de alquiler <span required="" aria-required="true">*</span></label>
      <select data-placeholder="Seleccione" name="campo[lista_precio_alquiler_id]" class="form-control chosen-select" data-rule-required="true" v-model="formulario.lista_precio_alquiler_id" :disabled="disabledEditar">
          <option value="">Seleccione</option>
          <option value="{{option.id}}" v-for="option in catalogoFormulario.lista_precio_alquiler" track-by="$index">{{option.nombre}}</option>
      </select>
  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">

  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" style="clear: both;">
      <label for="centro_contable_id">Centro contable <span required="" aria-required="true">*</span></label>
      <select id="centro_contable_id" data-placeholder="Seleccione" name="campo[centro_contable_id]" class="form-control select2" data-rule-required="true" v-model="formulario.centro_contable_id" :disabled="disabledEditar" v-select3="formulario.centro_contable_id">
          <option value="">Seleccione</option>
          <option value="{{centro_contable.centro_contable_id}}" v-for="centro_contable in catalogoFormulario.centro_contables | orderBy 'nombre'">{{centro_contable.nombre}}</option>
      </select>
      <label id="centro_contable_id-error" class="error" for="centro_contable_id"></label>
  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="centro_facturacion_id">Centro de facturaci&oacute;n </label>
      <select data-placeholder="Seleccione" name="campo[centro_facturacion_id]" class="form-control chosen-select" v-model="formulario.centro_facturacion_id" :disabled="disabledEditar || (catalogoFormulario.centro_facturable.length === 0)">
          <option value="">Seleccione</option>
          <option :value="centro_facturacion.id" v-for="centro_facturacion in catalogoFormulario.centro_facturable">{{centro_facturacion.nombre}}</option>
      </select>
  </div>

  <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
      <label for="estado_id">Estado <span required="" aria-required="true">*</span></label>
      <select data-placeholder="Seleccione" name="campo[estado]" class="form-control chosen-select" required="" data-rule-required="true" v-model="formulario.estado" :disabled="campoDisabled.estadoDisabled || disabledEditar">
          <option value="">Seleccione</option>
          <option value="{{estado.etiqueta}}" v-for="estado in catalogoFormulario.estados">{{estado.valor}}</option>
      </select>
  </div>

</div>
