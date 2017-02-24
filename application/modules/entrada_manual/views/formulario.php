<?php
//$info = !empty($info) ? $info : array();
//Template::cargar_formulario($info);
?>

<detalle :config.sync="config" :detalle.sync="detalle"></detalle>
<br>
<transacciones :config.sync="config" :detalle.sync="detalle"></transacciones>

<div class="row">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <a href="<?php echo base_url('entrada_manual/listar')?>" class="btn btn-default btn-block btn-facebook">
            <i class="fa fa-ban"> </i> Cancelar
        </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <button type="submint" class="btn btn-success btn-block btn-facebook" :disabled="config.disableDetalle || diffDebitoCredito">
            <i class="fa fa-save"> </i> Guardar
        </button>
    </div>
</div>
