
<entrega_items></entrega_items>



<div class="form-group col-xs-5 col-sm-5 col-md-5 col-lg-5 ">
    <label for="observaciones">Observaciones</label>
    <textarea name="campo[observaciones]" class="form-control" v-model="entrega_alquiler.observaciones" :disabled="disabledEditar"></textarea>
</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <a href="<?php echo base_url('entregas_alquiler/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden" name="campo[id]" id="entrega_alquiler_id" value="{{entrega_alquiler.id}}" />
        <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar"  :disabled="disabledEditar"/>
    </div>
</div>
