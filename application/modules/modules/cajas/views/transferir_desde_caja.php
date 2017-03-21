<div id="wrapper">
        <?php
        Template::cargar_vista('sidebar');
        ?>
        <div id="page-wrapper" class="gray-bg row">

            <?php Template::cargar_vista('navbar'); ?>
            <div class="row border-bottom"></div>
            <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

            <div class="col-lg-12">
                <div id="appOrdenventa" class="wrapper-content">
                    <div class="row">
                        <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                        </div>
                    </div>

                     <?php echo modules::run('cajas/ocultoformulariodesdecaja', $caja_uuid); ?>
                </div>

            </div><!-- cierra .col-lg-12 -->
        </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

    <?php
    echo Modal::config(array(
        "id" => "optionsModal",
        "size" => "sm"
    ))->html();
    ?>

 