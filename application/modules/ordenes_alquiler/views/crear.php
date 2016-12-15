<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="form_crear_ordenventa_div">
                <div class="row">
                    <div id="mensaje_info"></div>
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["mensaje"] : '' ?>
                    </div>
                </div>
                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'form_crear_ordenventa',
                    'autocomplete' => 'off'
                );
                echo form_open(base_url('ordenes_alquiler/guardar'), $formAttr);
                ?>

                <!--componente empezar desde-->
                <empezar_desde :empezable.sync="empezable" :detalle.sync="detalle" :config="config"></empezar_desde>

                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Datos de la orden de venta</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <?php
                    $info = !empty($info) ? array("info" => $info) : array();
                    echo modules::run('ordenes_alquiler/ocultoformulario', $info);
                    ?>

                </div>
                <?php echo form_close(); ?>

            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
