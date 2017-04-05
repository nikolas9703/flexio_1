

<?php if(is_array($id) || (!is_array($id) && $pago->estado != 'por_aplicar' && $pago->estado != 'aplicado')): ?>
<a href="#" class="btn btn-block btn-outline btn-warning state-btn" data-estado="por_aplicar" data-id="<?php echo !is_array($id) ? $id : ''?>">Por aplicar</a>
<?php endif;?>

<?php if(is_array($id) || (!is_array($id) && $pago->estado != 'aplicado')): ?>
<a href="#" class="btn btn-block btn-outline btn-success state-btn" data-estado="aplicado" data-id="<?php echo !is_array($id) ? $id : ''?>">Aplicado</a>
<?php endif;?>

<?php if(is_array($id) || (!is_array($id))): //&& $pago->estado == 'aplicado'?> 
<a href="#" class="btn btn-block btn-outline btn-danger state-btn" data-estado="anulado" data-id="<?php echo !is_array($id) ? $id : ''?>">Anulado</a>
<?php endif;?>

