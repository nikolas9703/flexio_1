<?php
$formAttr = array('method' => 'POST', 'id' => 'formExportarCuentasContables','autocomplete'  => 'off');
echo form_open(base_url('contabilidad/exportar_cuenta'), $formAttr);
?>
    <input type="hidden" name="nombre_cuenta" id="nombre_cuenta" value="" />
    <input type="hidden" name="codigo_cuenta" id="codigo_cuenta" value="" />
    <input type="hidden" name="estado_cuenta" id="estado_cuenta" value="" />
    <input type="hidden" name="centro_id_cuenta" id="centro_id_cuenta" value="" />
<?php echo form_close(); ?>