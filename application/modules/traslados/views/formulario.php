

<!--componente detalle-->
<detalle :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></detalle>

<!--componente articulos-->
<articulos :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></articulos>

<div class="row"></div>

<div class="row">
    <div class="col-lg-12">
        <label>Observaciones </label>
        <textarea name="campo[observaciones]" style="width: 50%;height: 100px;" type="textarea" class="form-control observaciones" id="campo[observaciones]" v-model="detalle.observaciones" :disabled="config.disableDetalle"></textarea>
        <div class="clearfix">&nbsp;</div>
    </div>
</div>

<div class="row">
    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <a href="<?php echo base_url('traslados/listar')?>" class="btn btn-default btn-block">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <input type="submit" value="Guardar" class="btn btn-primary btn-block" :disabled="config.disableDetalle">
        <input type="hidden" name="campo[id]" :value="detalle.id">
    </div>
</div>
