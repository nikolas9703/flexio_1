<?php
$formAttr = array('method' => 'POST', 'id' => 'formExportarEstadoCuenta','autocomplete'  => 'off');
echo form_open(base_url('subcontratos/exportar_subcontrato_estado_cuenta'), $formAttr);
?>
<input type="hidden" name="subcontrato_id" id="subcontrato_id" value="" />
<?php echo form_close(); ?>