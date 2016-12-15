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

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>&nbsp;</label>
        <select class="form-control" name="campo[tipo_deposito]" id="deposito_tipo" v-model="formulario.tipo_deposito">
            <option v-for="tipo in catalogoFormulario.tipoable" :value="tipo.etiqueta">{{tipo.valor}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label for="depositable_id">&nbsp;</label>
        <select name="campo[depositable_id]" v-model="formulario.depositable_id" class="form-control" id="depositable_id" data-rule-required="true" :disabled="catalogoFormulario.depositable === 0 || Desabilitados">
            <option value="">Seleccione</option>
            <option v-for="depositable in catalogoFormulario.depositable" :value="depositable.id" v-text="depositable.nombre"></option>
        </select>
        <label id="depositable_id-error" class="error" for="depositable_id"></label>
        <!-- <input type="hidden" name="pb1" value="{{formulario.depositable_type}}"> -->
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Estado</label>
        <select name="campo[estado]" v-model="formulario.estado" class="form-control" id="estado" data-rule-required="true" :disabled="campoDisabled.estadoDisabled" @change="cambiarEstadoAnticipo(formulario.estado)">
            <option v-for="estados in filtroEstados" :value="estados.etiqueta" v-text="estados.valor"></option>
        </select>

    </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Método de anticipo <span required="" aria-required="true">*</span></label>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <select class="form-control" name="campo[metodo_anticipo]" id="metodo_anticipo" v-model="formulario.metodo_anticipo" data-rule-required="true" :disabled="Desabilitados">
                <option value="">Seleccione</option>
                <option v-for="metodo_anticipo in filtroMetodoAnticipo" :value="metodo_anticipo.etiqueta" v-text="metodo_anticipo.valor"></option>
            </select>
        </div>
</div>


    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" v-if="formulario.metodo_anticipo ==='ach'">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Banco del <span v-text="owner"></span></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <select name="metodo_anticipo[nombre_banco_ach]" id="nombre_banco_ach" class="form-control" v-model="formulario.opciones_metodo_acticipo.ach.nombre_banco_ach" >
                <option value=""></option>
                <option v-for="banco in catalogoFormulario.bancos" :value="banco.id" v-text="banco.nombre"></option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Número de cuenta del <span v-text="owner"></span></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <input type="text" name="metodo_anticipo[cuenta]" id="cuenta_proveedor" class="form-control" v-model="formulario.opciones_metodo_acticipo.ach.cuenta" />
        </div>
    </div>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12"v-if="formulario.metodo_anticipo ==='cheque'">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Número Cheque</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <input type="text" name="metodo_anticipo[numero_cheque]" id="numero_cheque" class="form-control disable" disabled="" v-model="formulario.opciones_metodo_acticipo.cheque.numero_cheque"/>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <label>Nombre Banco</label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <input type="text" name="metodo_anticipo[nombre_banco_cheque]" id="nombre_banco_cheque" class="form-control" v-model="formulario.opciones_metodo_acticipo.cheque.nombre_banco_cheque"/>
        </div>
    </div>

  <!-- <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" v-if="formulario.metodo_anticipo ==='tarjeta_credito'">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de tarjeta</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <input type="text" name="metodo_anticipo[numero_tarjeta]" id="numero_tarjeta" class="form-control" />
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <label>Número de recibo</label>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <input type="text" name="metodo_anticipo[numero_recibo]" id="numero_recibo" class="form-control" />
    </div>
  </div> -->


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
