

<div class="ibox-content" style="display:block;">

    <detalle :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></detalle>

    <div class="row">
        <div class="col-lg-12">
            <h4 class="m-b-xs">Items de venta <span required="" aria-required="true">*</span></h4>
            <div class="hr-line-dashed m-t-xs"></div>
        </div>
    </div>
    <!--componente articulos de la orden de compra-->
    <articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></articulos>

    <div class="row"></div>

    <div class="row">
        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <a href="<?php echo base_url('ordenes_ventas/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <input type="submit" value="Guardar " class="btn btn-primary form-control" :disabled="config.disableDetalle">
            <input type="hidden" name="campo[id]" value="{{detalle.id}}">
            <input type="hidden" name="campo[vista]" value="{{config.vista}}">
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
            <label>Observaciones </label>
            <textarea id="comentario" name="campo[comentario]"  class="form-control observaciones" v-model="detalle.observaciones" :disabled="config.disableDetalle"></textarea>
            <label id="comentario-error" class="error" for="comentario"></label>
        </div>
    </div>
<input type="hidden" id="cliente_ID" value="" />
</div>

<style type="text/css">
    .observaciones{
        width: 30%;
        height: 100px !important;
    }
</style>
