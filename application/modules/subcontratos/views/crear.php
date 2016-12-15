<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="form_crear_subcontrato_div">
                <?php
                $formAttr = array(
                    'method'       => 'POST',
                    'id'           => 'form_crear_subcontrato',
                    'autocomplete' => 'off'
                    );
                    echo form_open(base_url('subcontratos/guardar'), $formAttr);?>

                    <div class="ibox border-bottom">
                        <div class="ibox-title">
                            <h5>Detalle Subcontrato</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>

                        <div class="ibox-content" style="display:block;">
                            <div class="row">
                                <?php
                                $info = !empty($info) ? array("info" => $info) : array();
                                echo modules::run('subcontratos/ocultoformulario', $info);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php  echo  form_close();?>
                </div>

            </div><!-- cierra .col-lg-12 -->
        </div><!-- cierra #page-wrapper -->
    </div><!-- cierra #wrapper -->
    <?php
    echo Modal::config(array(
        "id" => "opcionesModal",
        "size" => "sm"
        ))->html();
