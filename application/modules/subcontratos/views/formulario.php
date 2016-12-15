

<detalle :config.sync="config" :catalogos="catalogos" :detalle.sync="detalle"></detalle>

<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">

</div>

<montos :config.sync="config" :catalogos="catalogos" :detalle.sync="detalle"></montos>

<movimientos :config.sync="config" :catalogos="catalogos" :detalle.sync="detalle"></movimientos>



<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" v-show="!config.agregar_adenda">
    <div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
    <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
        <a href="<?php echo base_url('subcontratos/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
        <input type="hidden" name="campo[id]" value="{{detalle.id}}"/>
        <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" :disabled="config.disableDetalle || !validate_montos || !validate_porcentajes" value="Guardar" :disabled="config.disableDetalle"/>
    </div>
</div>
