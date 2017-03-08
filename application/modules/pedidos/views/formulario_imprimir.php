<?php
$formAttr = array('method' => 'POST', 'id' => 'formImprimirPedido','autocomplete'  => 'off');
echo form_open(base_url('pedidos/imprimir_pedido'), $formAttr);
?>
<input type="hidden" name="pedido" id="pedido" value="" />
<?php echo form_close(); ?>