<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">

                    <div class="hide filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">

                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <label>Empezar interés asegurado desde</label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <select id="formulario" class="white-bg chosen-filtro" role="tablist">
                                <option value="">Seleccione</option>
                                <?php
                                if(!empty($campos['campos']['tipos_intereses_asegurados'])){
                                    foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
                                        echo '<option value="'. $tipo->valor .'Tab">'. $tipo->etiqueta .'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>

                        <!-- Hide Nav-Tabs -->
                        <ul class="nav nav-tabs hide">
                            <?php
                            if(!empty($campos['campos']['tipos_intereses_asegurados'])){
                                foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
                                    echo '<li><a href="#'.$tipo->valor.'Tab" data-toggle="tab">'.$tipo->etiqueta.'</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Tabs Content -->
                    <div class="tab-content filtro-formularios-content m-t-sm">
                    <?php
                    foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
                    ?>
                    <div class="tab-pane" id="<?=$tipo->valor?>Tab">
                    <?php
                    echo modules::run("intereses_asegurados/" . $tipo->valor. "formularioparcial",$campos);
                    ?>
                    </div>
                        <?php
                            }
                        ?>

                    </div>

                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<!-- inicia #crearModal -->
<div class="modal fade bs-example-modal-sm" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="crearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Interés Asegurado: Crear</h4>
            </div>


            <div class="modal-body">
                <?php foreach ($campos['campos']['tipos_intereses_asegurados'] as $tipo):?>
                    <a href="<?php echo base_url("intereses_asegurados/crear/".$tipo->valor); ?>" id="" class="btn btn-block btn-outline btn-success"><?php echo $tipo->etiqueta; ?></a>
                <?php endforeach;?>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- termina #optionsModal -->