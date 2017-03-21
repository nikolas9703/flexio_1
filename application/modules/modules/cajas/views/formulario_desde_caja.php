 <?php $attrs = [
            'id' => 'crearDesdeCajaForm',
            'autocomplete' => 'off',
            'method' => 'POST'
        ];?>

        <?php echo form_open(base_url('cajas/guardar-transferir-desde'), $attrs)?>

            <!--componente empezar desde-->
            <empezar_desde :empezable.sync="empezable" :detalle.sync="detalle" :config="config"></empezar_desde>


 
            <div class="ibox">

                <div class="ibox-title border-bottom">
                    <h5>Transferir desde {{detalle.nombre_caja_desde}}: Crear</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content m-b-sm" style="display: block; border:0px">

  
                    <!--componente articulos de la orden de compra-->
      <detalle :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></detalle>
 
                    <div class="row">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <a href="<?php echo base_url('cajas/listar')?>" class="btn btn-default form-control">Cancelar </a>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <input type="submit" value="Guardar " class="btn btn-primary form-control" :disabled="config.botonGuardarDisabled">
                            <input type="hidden" name="campo[id]" value="{{detalle.id}}">
                        </div>
                    </div>



                </div>

            </div>
        <?php echo form_close();?>

 