

<detalle :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos"></detalle>

<pagables :config.sync="config" :detalle.sync="detalle"></pagables>

<monto :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos"></monto>

<metodos_pago :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos"></metodos_pago>



<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <a href="<?php echo base_url('pagos/listar');?>" class="btn btn-default btn-block">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden" name="campo[id]" value="{{detalle.id}}"/>
        <input type="submit" class="btn btn-primary btn-block" value="Guardar" :disabled="!sePuedeGuardar"/>
    </div>
</div>
