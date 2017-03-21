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
                    <div id="mensaje">
                    </div>
                </div>
                <div ng-controller="toastController"></div>

                <div class="ibox">
                    <!-- Tab panes -->
                    <div class="ibox-content" >

                    <?php
                        $formAttr = array(
                            'method' => 'POST',
                            'id' => 'crearComisionesForm',
                            'autocomplete' => 'off'
                        );
                        echo form_open(base_url(uri_string()), $formAttr);
                    ?>
                        <div class="row">
                           <?php
								echo modules::run('comisiones_seguros/ocultoformulario',$campos);
							?>
                        </div>
                    <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
?>