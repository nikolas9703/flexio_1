<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="cliente_id">Cliente <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[cliente_id]" class="form-control chosen-select" required="" data-rule-required="true" v-model="oportunidad.empezar_desde_id" :disabled="config.vista == 'editar' || oportunidad.empezar_desde_type == '' || disabledHeader || disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{cliente.id}}" v-for="cliente in clientes | orderBy 'nombre'" v-if="oportunidad.empezar_desde_type=='cliente'">{{cliente.nombre}}</option>
            <option value="{{cliente_potencial.id_cliente_potencial}}" v-for="cliente_potencial in clientes_potenciales | orderBy 'nombre'" v-if="oportunidad.empezar_desde_type=='cliente_potencial'">{{cliente_potencial.nombre}}</option>
        </select>
    </div>
    
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label for="nombre">Nombre de la oportunidad <span required="" aria-required="true">*</span></label>
        <input type="text" name="campo[nombre]" class="form-control" required="" data-rule-required="true" v-model="oportunidad.nombre">
    </div>
    
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label for="monto">Monto de la oportunidad</label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campo[monto]" class="form-control" v-model="oportunidad.monto" v-inputmask="oportunidad.monto" :config="{'mask':'9{1,8}[.9{0,2}]','greedy':false}">
        </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label for="fecha_cierre">Fecha esperada de cierre <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="input-left-addon" name="campo[fecha_cierre]" class="form-control" required="" data-rule-required="true" v-model="oportunidad.fecha_cierre" v-datepicker2="oportunidad.fecha_cierre" :config="{dateFormat: 'dd/mm/yy'}">
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="asignado_a_id">Asignado a <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[asignado_a_id]" class="form-control chosen-select" data-rule-required="true" v-model="oportunidad.asignado_a_id" :disabled="disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{vendedor.id}}" v-for="vendedor in vendedores | orderBy 'nombre' 'apellido'">{{vendedor.nombre +' '+ vendedor.apellido}}</option>
        </select>
    </div>
    
    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label for="etapa_id">Etapa <span required="" aria-required="true">*</span></label>
        <select data-placeholder="Seleccione" name="campo[etapa_id]" class="form-control chosen-select" required="" data-rule-required="true" v-model="oportunidad.etapa_id" :disabled="disabledEstado || disabledEditar">
            <option value="">Seleccione</option>
            <option value="{{estado.id}}" v-for="estado in estados | orderBy 'id'">{{estado.nombre}}</option>
        </select>
    </div>

</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div"> 
    
    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
    
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <a href="<?php echo base_url('oportunidades/listar')?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> 
    </div>
    
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <input type="hidden" name="campo[crear_cotizacion]" id="crear_cotizacion" value="0">
        <input type="hidden" name="campo[id]" v-model="oportunidad.id">
        <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]">
    </div>
    
</div>



