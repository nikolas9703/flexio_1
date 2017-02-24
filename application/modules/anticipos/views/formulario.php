<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="proveedor"><span v-text="owner"></span> <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[anticipable_id]" class="form-control select2" id="proveedor" data-rule-required="true" v-model="formulario.anticiplable_id" v-select3="formulario.anticipable_id"
        :disabled="campoDisabled.anticipable || campoDisabled.camposEditar" :config="{width:'100%'}">
          <option value="">Seleccione</option>
          <option v-for="tipo in catalogoFormulario.anticipables" :value="tipo.id">{{tipo.nombre}}</option>
        </select>
        <label id="proveedor-error" class="error" for="proveedor"></label>
        <input type="hidden" name="campo[tipo_anticipable]" value="{{formulario.tipo_anticipable}}">
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="fecha_anticipo">Fecha de anticipo <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_anticipo]" class="form-control"  id="fecha_anticipo" data-rule-required="true"  v-datepicker2="formulario.fecha_anticipo" :config="{dateFormat: 'dd/mm/yy'}" :disabled="campoDisabled.camposEditar">
        </div>
        <label id="fecha_abono-error" class="error" for="fecha_anticipo"></label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>&nbsp;</label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled v-model="formulario.saldo_pendiente" class="form-control moneda"  id="campo[saldo]" style="text-align: left;">
        </div>
        <label class="label-danger-text">Saldo por pagar</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>&nbsp;</label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled v-model="formulario.credito" class="form-control moneda" id="campo[lcredito]">
        </div>
        <label class="label-success-text">Crédito a favor</label>
    </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label for="total">Monto del anticipo</label>
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" id="total" name="campo[monto]" v-model="formulario.monto" class="form-control moneda" data-rule-required="true" data-rule-min="0.01" :disabled="Desabilitados"/>
            </div>
            <label id="total-error" class="error" for="total"></label>
            <label class="error" v-if="validacionMonto" >El monto no puede ser mayor al monto de origen</label>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
        <label>Creado por</label>
        <select name="campo[creado_por]" class="form-control" id="creado_por" v-model="formulario.creado_por"  :disabled="true">
            <option value="">Seleccione</option>
            <option :value="comprador.id" v-for="comprador in catalogoFormulario.compradores">{{comprador.nombre}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Estado</label>
        <select name="campo[estado]" v-model="formulario.estado" class="form-control" id="estado" data-rule-required="true" :disabled="campoDisabled.estadoDisabled" @change="cambiarEstadoAnticipo(formulario.estado)">
            <option v-for="estados in filtroEstados" :value="estados.etiqueta" v-text="estados.valor"></option>
        </select>

    </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:30px">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('anticipos/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <input type="hidden" name="campo[id]" id="id" value="{{formulario.id}}" :disabled="formulario.id ===''"/>
     <button class="btn btn-primary btn-block" name="guardarBtn" id="guardarBtn"  @click="guardar()" :disabled="campoDisabled.botonDisabled || validacionMonto || isAnuladoOrAprobado"><span>Guardar</span></button>
    </div>
</div>
<!-- Termina campos de Busqueda -->
