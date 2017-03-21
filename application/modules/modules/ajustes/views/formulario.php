<div class="ibox-content m-b-sm" style="display: block; border:0px">

    <detalle :config.sync="config" :detalle.sync="detalle", :catalogos="catalogos"></detalle>
    <articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos"></articulos>

    <div class="row">
        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <a href="<?php echo base_url("ajustes/listar")?>" class="btn btn-default btn-block">Cancelar </a>
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input class="btn btn-primary btn-block" type="submit" value="Guardar" :disabled="config.disableDetalle || getItemNoValido">
            <input type="hidden" name="campo[id]" value="{{detalle.id}}">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <label>Observaciones </label>
            <textarea name="campo[descripcion]" cols="40" rows="10" type="textarea" class="form-control observaciones" v-model="detalle.descripcion" :disabled="config.disableDetalle"></textarea>
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

</div>
