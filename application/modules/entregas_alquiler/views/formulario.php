<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control chosen-select" required="" data-rule-required="true" v-model="entrega_alquiler.cliente_id" :disabled="true">
            <option value="">Seleccione</option>
            <option value="{{cliente.id}}" v-for="cliente in clientes | orderBy 'nombre'">{{cliente.nombre}}</option>
        </select>
    </div>

    <!--<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label for="fecha_contrato_alquiler">Fecha del contrato de alquiler</label>
        <input type="text" disabled name="campo[fecha_contrato_alquiler]" class="form-control" v-model="entrega_alquiler.fecha_contrato_alquiler">
    </div>-->

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
      <label for="">Fecha de inicio y fin de contrato</label>
      <div class="input-group">
        <div class="input-group-addon"><span class="fa fa-calendar"></span></div>
        <input type="input-left-addon" name="campo[fecha_inicio_contrato]" id="fecha_inicio" class="form-control" v-model="entrega_alquiler.fecha_inicio_contrato" disabled>
        <span class="input-group-addon">a</span>
        <input type="input-left-addon" name="campo[fecha_fin_contrato]" id="fecha_final" class="form-control" v-model="entrega_alquiler.fecha_fin_contrato" disabled>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" disabled class="form-control debito" value="{{entrega_alquiler.saldo_cliente | currency ''}}">
        </div>
        <label class="label-danger-text">Saldo por cobrar</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" disabled class="form-control debito" value="{{entrega_alquiler.credito_cliente | currency ''}}">
        </div>
        <label class="label-success-text">Crédito a favor</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label for="fecha_entrega">Fecha y hora de entrega <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="campo[fecha_entrega]" class="form-control fecha_entrega" required="" data-rule-required="true" v-model="entrega_alquiler.fecha_entrega" :disabled="disabledEditar">
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="created_by">Entregado por <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[created_by]" class="form-control chosen-select" data-rule-required="true" v-model="entrega_alquiler.created_by" :disabled="disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{creador.id}}" v-for="creador in usuarios | orderBy 'nombre' 'apellido'">{{creador.nombre +' '+ creador.apellido}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="vendedor_id">Vendedor <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[vendedor_id]" class="form-control chosen-select" data-rule-required="true" v-model="entrega_alquiler.vendedor_id" :disabled="true">
            <option value="">Seleccione</option>
            <option value="{{vendedor.id}}" v-for="vendedor in vendedores | orderBy 'nombre' 'apellido'">{{{vendedor.nombre}}}{{{vendedor.apellido != undefined ? ' '+ vendedor.apellido : ''}}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="estado_id">Estado <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[estado_id]" class="form-control chosen-select" required="" data-rule-required="true" v-model="entrega_alquiler.estado_id" :disabled="disabledEstado || disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{estado.id}}" v-for="estado in estados | orderBy 'id'">{{estado.nombre}}</option>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 " style="display:none;">
        <label for="codigo">N&uacute;mero de entrega <span required="" aria-required="true">*</span></label>
        <input type="text" disabled name="campo[codigo]" class="form-control" v-model="entrega_alquiler.codigo">
    </div>

</div>
