
<?php if(is_array($id) || (!is_array($id) && $termino_condicion->estado != 'activo')): ?>
<a href="#" class="btn btn-block btn-outline btn-success state-btn" data-estado="activo" data-id="<?php echo !is_array($id) ? $id : ''?>">Activo</a>
<?php endif;?>

<?php if(is_array($id) || (!is_array($id) && $termino_condicion->estado != 'inactivo')): ?>
<a href="#" class="btn btn-block btn-outline btn-danger state-btn" data-estado="inactivo" data-id="<?php echo !is_array($id) ? $id : ''?>">Inactivo</a>
<?php endif;?>
