

    <!--componente articulo-agrupador-->
    <articulo-agrupador :catalogos="catalogos" v-for="row in contrato_alquiler.articulos"
        :parent_index="$index"
        :detalle="contrato_alquiler"
        :row.sync="row"
        :mostrar="mostrar"
        :desabilitar="desabilitar"
        :configv="config"
        ></articulo-agrupador>



<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
    <label for="observaciones">Observaciones</label>
    <textarea name="campo[observaciones]" class="form-control" v-model="contrato_alquiler.observaciones" :disabled="disabledEditar"></textarea>
</div>


<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 last-div">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <a href="<?php echo base_url('contratos_alquiler/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden" name="campo[id]" id="contrato_alquiler_id" value="{{contrato_alquiler.id}}" />
        <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Guardar"  :disabled="disabledEditar || getItemsDuplicados"/>
    </div>
</div>
