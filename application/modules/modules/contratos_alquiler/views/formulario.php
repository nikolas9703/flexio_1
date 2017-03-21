
<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control chosen-select" required="" data-rule-required="true" v-model="contrato_alquiler.cliente_id" v-select2="contrato_alquiler.cliente_id"  :disabled="config.disabledClienteId || disabledHeader || disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{cliente.id}}" v-for="cliente in clientes | orderBy 'nombre'">{{cliente.nombre}}</option>
        </select>
    </div>
    <!--<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label>Fecha de inicio de contrato <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="input-left-addon" name="campo[fecha_inicio]" id="fecha_inicio" class="form-control" v-model="contrato_alquiler.fecha_inicio" v-datepicker2="contrato_alquiler.fecha_inicio"  :config="{dateFormat: 'dd/mm/yy'}">
        </div>
    </div>-->

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
      <label for="">Fecha de inicio y fin de contrato</label>
      <div class="input-group">
        <div class="input-group-addon"><span class="fa fa-calendar"></span></div>
        <input type="input-left-addon" name="campo[fecha_inicio]" id="fecha_inicio" class="form-control" v-model="contrato_alquiler.fecha_inicio" v-datepicker-range="contrato_alquiler.fecha_inicio" :rangeto="contrato_alquiler.fecha_inicio">
        <span class="input-group-addon">a</span>
        <input type="input-left-addon" name="campo[fecha_fin]" id="fecha_final" class="form-control" v-model="contrato_alquiler.fecha_fin" v-datepicker-range="contrato_alquiler.fecha_fin" :rangeto="contrato_alquiler.fecha_fin">
      </div>
    </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled="" v-model="contrato_alquiler.saldo" class="form-control debito">
        </div>
        <label class="label-danger-text">Saldo por cobrar</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" disabled="" v-model="contrato_alquiler.credito" class="form-control credito">
        </div>
        <label class="label-success-text">Cr&eacute;dito a favor</label>
    </div>

      <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="centro_contable_id">Centro contable <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[centro_contable_id]" class="form-control chosen-select"  data-rule-required="true"  v-model="contrato_alquiler.centro_contable_id" :disabled="disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{centro_contable.id}}"  v-for="centro_contable in centros_contables | orderBy 'nombre'">{{centro_contable.nombre}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="centro_facturacion_id">Centro de facturaci&oacute;n <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione"  data-rule-required="true" name="campo[centro_facturacion_id]" class="form-control chosen-select" v-model="contrato_alquiler.centro_facturacion_id" :disabled="disabledEditar">
            <option value="">Seleccione</option>
            <option :value="centro_facturacion.id" v-for="centro_facturacion in contrato_alquiler.centros_facturacion">{{centro_facturacion.nombre}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" v-bind:style="{display: showFacturacion}">
        <label for="corte_facturacion_id">Facturaci&oacute;n recurrente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" required="" name="campo[corte_facturacion_id]" class="form-control" v-model="contrato_alquiler.corte_facturacion_id" :disabled="disabledEditar" v-on:change="cambioDeCorteFacturacion(contrato_alquiler.corte_facturacion_id)">
            <option value="">Seleccione</option>
            <option :value="corte_facturacion.id" v-for="corte_facturacion in cortes_facturacion">{{corte_facturacion.nombre}}</option>
        </select>
    </div>
     <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="dia_corte">Corte de facturaci&oacute;n <span required="" v-bind:style="{display: dispStyle}" aria-required="true">*</span></label>
      <!-- <input type='text' data-placeholder="Seleccione" name="campo[dia_corte]" class="form-control" v-model="contrato_alquiler.dia_corte" :disabled="contrato_alquiler.corte_facturacion_id!='11' || disabledEditar">-->
        <select data-placeholder="Seleccione" name="campo[dia_corte]" class="form-control" v-model="contrato_alquiler.dia_corte" :data-rule-required="corte_dia_req ? 'true' : 'false'" :aria-required="corte_dia_req ? 'true' : 'false'" :disabled="disabledCorteFacturacion  || disabledEditar">
            <option value=''>Seleccione</option>
            <option value="{{dia.id}}"  v-for="dia in dia_corte">{{dia.nombre}}</option>
        </select>

    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="centro_facturacion_id">Facturar contra entrega <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione"  data-rule-required="true" name="campo[facturar_contra_entrega_id]" class="form-control chosen-select" v-model="contrato_alquiler.facturar_contra_entrega_id">
            <option value="">Seleccione</option>
            <option :value="option.id" v-for="option in preguntas_cerrada">{{option.nombre}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="centro_facturacion_id">Modelo de c&aacute;lculo de costos al retornar <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione"  data-rule-required="true" name="campo[calculo_costo_retorno_id]" class="form-control chosen-select" v-model="contrato_alquiler.calculo_costo_retorno_id">
            <option value="">Seleccione</option>
            <option :value="option.id" v-for="option in costos_retorno">{{{option.nombre}}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" ><!-- style='clear: both;' -->
        <label for="vendedor_id">Lista de precio de alquiler <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[lista_precio_alquiler_id]" class="form-control chosen-select" data-rule-required="true" v-model="contrato_alquiler.lista_precio_alquiler_id" :disabled="disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{option.id}}" v-for="option in lista_precio_alquiler" track-by="$index">{{option.nombre}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" ><!-- style='clear: both;' -->
        <label for="vendedor_id">Vendedor <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[vendedor_id]" class="form-control chosen-select" data-rule-required="true" v-model="contrato_alquiler.vendedor_id" :disabled="disabledEditar || disabledVendedor">
            <option value="">Seleccione</option>
            <option value="{{vendedor.id}}" v-for="vendedor in vendedores | orderBy 'nombre' 'apellido'" track-by="$index">{{{vendedor.nombre}}}{{{vendedor.apellido != undefined ? ' '+ vendedor.apellido : ''}}}</option>
        </select>
    </div>

    <!--<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">-->
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="referencia">Referencia</label>
        <input type="text" name="campo[referencia]" class="form-control" v-model="contrato_alquiler.referencia" :disabled="disabledEditar">
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="estado_id">Estado <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[estado_id]" class="form-control chosen-select" required="" data-rule-required="true" v-model="contrato_alquiler.estado_id" :disabled="disabledEstado || disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{option.id}}" v-for="option in getEstados">{{option.nombre}}</option>
        </select>
    </div>
    <!-- <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " style="display:none;">
        <label for="codigo">N&uacute;mero de contrato <span required="" aria-required="true">*</span></label>
        <input type="text" disabled name="campo[codigo]" class="form-control" v-model="contrato_alquiler.codigo">
    </div>
     -->

</div>
