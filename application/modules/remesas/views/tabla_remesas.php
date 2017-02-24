<?php
    $formAttr = array(
        'method' => 'POST',
        'id' => 'remesa_pagar',
        'autocomplete' => 'off'
    );
?>
<?php echo form_open(base_url('remesas/guardar'), $formAttr); ?>
<div class="row hidden" style="overflow:auto;" id="tabla_remesas"> <!-- style="margin-top:-50px;"  -->
    <table class="table" id="facturaItems" >
        <thead>
            <tr>
            <th width="14%">No. Recibo</th>
            <th width="14%">No. Póliza</th>
            <th width="14%">Ramo</th>
            <th width="14%">Asegurado</th>
            <th width="14%">Inicio vigencia</th>
            <th width="14%">Fin vigencia</th>
            <th width="14%">Prima cobrada</th>
            <th width="14%">Impuesto</th>
            <th width="14%">Prima Neta</th>
            <th width="14%">% Comisión</th>
            <th width="14%">Comisión descontada</th>
            <th width="14%">% Sobre comisión</th>
            <th width="14%">S.Comisión descontada</th>
            <th width="14%">Pago a aseguradora</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="remesas in informacionRemesas" id="items{{$index}}" class="item-listing" style="background-color:#ffffff">
                <td style="{{remesas.estilos}}">{{remesas.codigo}}</td>
                <td style="{{remesas.estilos}}">{{remesas.numero_poliza}}</td>
                <td style="{{remesas.estilos}}">{{remesas.nombre_ramo}}</td>
                <td style="{{remesas.estilos}}">{{remesas.nombre_aseguradora}}</td>
                <td style="{{remesas.estilos}}">{{remesas.inicio_vigencia}}</td>
                <td style="{{remesas.estilos}}">{{remesas.fin_vigencia}}</td>
                <td style="{{remesas.estilos}}">{{remesas.prima_total}}</td>
                <td style="{{remesas.estilos}}">{{remesas.impuesto}}</td>
                <td style="{{remesas.estilos}}" >{{remesas.prima_neta}}</td>
                <td class="text-center" style="{{remesas.estilos}}">{{remesas.desc_comision}}</td>
                <td class="text-center" style="{{remesas.estilos}}">{{remesas.valor_descuento}}</td>
                <td class="text-center" style="{{remesas.estilos}}">{{remesas.sobre_comision}}</td>
                <td class="text-center" style="{{remesas.estilos}}">{{remesas.valor_sobreComision}}</td>
                <td class="text-center" style="{{remesas.estilos}}">{{remesas.total_aseguradora}}</td>
            </tr>
        </tbody>
    </table>

    <input v-for="cobro in cobros" type="hidden" name="id_cobros[]" id="id_cobros" value="{{cobro.id_cobro}}">
    <input type="hidden" name="remesas[codigo_remesa]" id="codigo_remesa" value="{{codigo_remesa}}">
    <input type="hidden" name="remesas[id_aseguradora]" id="id_aseguradora" value="">
    <input type="hidden" name="remesas[monto_remesa]" id="monto_remesa" value="">

    <input type="hidden" name="fecha_desde" id="fecha_desde_formulario" value="">
    <input type="hidden" name="fecha_hasta" id="fecha_hasta_formulario" value="">
    <input type="hidden" name="remesas[ramos]" id="id_ramos" value="">

    <div class="row"> 
        <div class="col-xs-0 col-sm-6 col-md-6 col-lg-6">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <a href="<?php echo base_url('remesas/listar'); ?>" class="btn btn-default btn-block cancelar" id="cancelar" >Cancelar </a> 
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="remesas[guardar]" id="guardar_remesa" value="Guardar " class="btn btn-primary btn-block" :disabled="disabledGuardar">
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="remesas[pagar]" id="pagar_remesa" value="Pagar" class="btn btn-primary btn-block" :disabled="disabledPagar">
        </div>
    </div>
<?php echo form_close(); ?>
    
</div>

