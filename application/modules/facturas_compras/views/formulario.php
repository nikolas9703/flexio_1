<?php
    $info = !empty($info) ? $info : array();
?>

<!--componente detalle orden de compra-->
<detalle :config="config" :detalle.sync="detalle" :catalogos="catalogos"></detalle>


<!--componente articulos de la orden de compra-->
<articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></articulos>


<div class="row"></div>

<div class="row">
    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <a href="<?php echo base_url('facturas_compras/listar')?>" class="btn btn-default form-control">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">{{desHabilitandoPorCantidad}}
        <input type="submit" value="Guardar " class="btn btn-primary form-control" :disabled="config.disableDetalle || disabledPorPolitica">
        <input type="hidden" name="campo[id]" value="{{detalle.id}}">
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <label>Observaciones </label>
        <textarea name="campo[observaciones]" cols="40" rows="10" type="textarea" class="form-control observaciones" id="campo[observaciones]" v-model="detalle.observaciones" :disabled="config.disableDetalle"></textarea>
        <div class="clearfix">&nbsp;</div>
    </div>
</div>

<div class="row"></div>

<style type="text/css">
    .observaciones{
        width: 30%;
        height: 100px !important;
    }
</style>
