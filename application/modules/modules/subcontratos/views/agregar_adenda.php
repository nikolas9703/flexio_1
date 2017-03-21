<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>



                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'form_crear_subcontrato',
                    'autocomplete' => 'off'
                );
                echo form_open(base_url('subcontratos/guardar'), $formAttr);
                ?>

                <div class="ibox float-e-margins" id="form_crear_subcontrato_div">
                    <div class="ibox-title">
                        <h5>Detalle subcontrato</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:none;">
                        <div class="row">
                            <?php
                            $info = !empty($subcontrato) ? $subcontrato : array();
                            echo modules::run('subcontratos/ocultoformulario', $info);
                            ?>
                        </div>
                    </div>
                </div>
<?php echo form_close(); ?>

                <div id="formulario_adenda">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Adendas</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>

                        <div class="ibox-content" style="display:inline-block">
                            <?php
                            $formAttr = array(
                                'method' => 'POST',
                                'id' => 'form_crear_adenda',
                                'autocomplete' => 'off'
                            );
                            echo form_open(base_url('subcontratos/guardar_adenda'), $formAttr);
                            $info_adenda = !empty($adenda) ? $adenda : array();
                            echo modules::run('subcontratos/ocultoformularioAdenda', $info_adenda);
                            echo form_close();
                            ?>
                        </div>
                    </div>
                </div>

        <!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
