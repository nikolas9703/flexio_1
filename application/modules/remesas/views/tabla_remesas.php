<?php
    $formAttr = array(
        'method' => 'POST',
        'id' => 'remesa_pagar',
        'autocomplete' => 'off'
    );
?>
<?php echo form_open(base_url('remesas/guardar'), $formAttr); ?>
<div class="row hidden" id="tabla_remesas"> <!-- style="margin-top:-50px;" overflow-x:scroll -->
    <table class="table" id="facturaItems" >
        <thead>
            <tr>
            <th width="7%">No. Recibo</th>
            <th width="7%">No. Póliza</th>
            <th width="14%">Ramo</th>
            <th width="7%">Asegurado</th>
            <th width="7%">Inicio vigencia</th>
            <th width="7%">Fin vigencia</th>
            <th width="7%">Prima total</th>
            <th width="7%">Impuesto</th>
            <th width="7%">Prima Neta</th>
            <th width="7%">% comision</th>
            <th width="7%">Comisión descontada</th>
            <th width="7%">% Sobre comisión</th>
            <th width="7%">S.Comisión descontada</th>
            <th width="7%">Pago a aseguradora</th>
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
    <input type="text" name="remesas[codigo_remesa]" id="codigo_remesa" value="{{codigo_remesa}}">
    <input type="hidden" name="remesas[id_aseguradora_guardar]" id="id_aseguradora_guardar" value="">
    <input type="hidden" name="remesas[monto_guardar]" id="monto_remesa_guardar" value="">
    <input type="hidden" name="remesas[vista]" id="vista" value="guardar">

    <input type="hidden" name="fecha_desde" id="fecha_desde_formulario" value="">
    <input type="hidden" name="fecha_hasta" id="fecha_hasta_formulario" value="">
    <input type="hidden" name="remesas[ramos]" id="id_ramos" value="">

    <div class="row"> 
        <div class="col-xs-0 col-sm-6 col-md-6 col-lg-6">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <a href="<?php echo base_url('remesas/listar'); ?>" class="btn btn-default btn-block cancelar" id="cancelar" >Cancelar </a> 
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="guardar" id="guardar_remesa" value="Guardar " class="btn btn-primary btn-block" :disabled="disabledGuardar">
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="pagar" id="pagar_remesa" value="Pagar" class="btn btn-primary btn-block" :disabled="disabledPagar">
        </div>
    </div>
<?php echo form_close(); ?>
    
    <div id="opciones_modal" style="display:none">
        <?php echo form_open(base_url('remesas/guardar'), $formAttr); ?>
        <input v-for="cobro in cobros" type="hidden" name="id_cobros[]" id="id_cobros" value="{{cobro.id_cobro}}">
        <input type="hidden" name="remesas[codigo_remesa]" id="codigo_remesa" value="{{codigo_remesa}}">
        <input type="hidden" name="remesas[id_aseguradora_pagar]" id="id_aseguradora_pagar" value="">
        <input type="hidden" name="remesas[monto_pagar]" id="monto_remesa_pagar" value="">
        <input type="hidden" name="remesas[vista]" id="vista" value="pagar">
        <input type="hidden" name="fecha_desde" id="fecha_desde_formulario1" value="">
        <input type="hidden" name="fecha_hasta" id="fecha_hasta_formulario1" value="">
        <input type="hidden" name="remesas[ramos]" id="id_ramos1" value="">

        <label>Forma de pago</label>
        <select id="forma_pago" name="remesas[forma_pago]" class="form-control" data-rule-required="true">
            <option value="">Seleccione</option>
            <option value="Efectivo">Efectivo</option>
            <option value="Cheque">Cheque</option>
            <option value="Transferencia">Transferencia</option>
        </select>
        <br>
        <label>Banco</label>
        <select name="remesas[banco]" id="banco" class="form-control" disabled>
            <option value="">Seleccione</option>
            <option v-for="banco in bancos" value="{{banco.id}}">{{banco.nombre}}</option>
        </select>
        <br>
        <label>N° cheque</label>
        <input type="text" class="form-control" name="remesas[numero_cheque]" id="numero_cheque" disabled>
        <br>
        <br>
        <input type="submit" name="procesar_remesa" id="procesar_remesa" value="Procesar" class="btn btn-primary btn-block" id="pagar_remesas" >
        <?php echo form_close(); ?>
    </div>
    
</div>

