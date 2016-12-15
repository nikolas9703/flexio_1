



        <?php $attrs = [
            'id' => 'crearOrdenesForm',
            'autocomplete' => 'off',
            'method' => 'POST'
        ];?>

        <?php echo form_open(base_url('ordenes/guardar'), $attrs)?>

            <!--componente empezar desde-->
            <empezar_desde :empezable.sync="empezable" :detalle.sync="detalle" :config="config"></empezar_desde>




            <div class="ibox">

                <div class="ibox-title border-bottom">
                    <h5>Datos de &oacute;rden de compra</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content m-b-sm" style="display: block; border:0px">

                    <!--componente detalle orden de compra-->
                    <detalle :config="config" :detalle.sync="detalle" :catalogos="catalogos"></detalle>

                    <!--componente articulos de la orden de compra-->
                    <articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></articulos>

                    <div class="row"></div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label>Observaciones </label>
                            <textarea name="campo[observaciones]" cols="40" rows="10" type="textarea" class="form-control observaciones" id="campo[observaciones]" v-model="detalle.observaciones" :disabled="config.disableDetalle"></textarea>
                            <div class="clearfix">&nbsp;</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <a href="<?php echo base_url('ordenes/listar')?>" class="btn btn-default form-control">Cancelar </a>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <input type="submit" value="Guardar " class="btn btn-primary form-control" :disabled="config.disableDetalle  || disabledPorPolitica ||   config.disableBotonForEstado== false">
                            <input type="hidden" name="campo[id]" value="{{detalle.id}}">
                            <input type="hidden" name="campo[correo_proveedor]" value="{{detalle_modal.correo}}">
                            <input type="hidden" name="campo[codigo]" value="{{detalle_modal.codigo}}">
                         </div>
                    </div>



                </div>

            </div>


        <?php echo form_close();?>



<style type="text/css">
    .observaciones{
        width: 30%;
        height: 100px !important;
    }
</style>
