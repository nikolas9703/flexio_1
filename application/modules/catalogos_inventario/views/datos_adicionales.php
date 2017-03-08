<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>

    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb?>

    	<div class="col-lg-12">
            <div class="wrapper-content" id="wrapper-content-div">

                <div class="ibox-title">
                    <h5><i class="fa fa-info-circle"></i>&nbsp;Datos adicionales de la categoría <small></small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        <div class="tab-content">
                            <div id="categoria_items" class="tab-pane active">

                                <details :config="config" :detalle="detalle"></details>
                                <hr><br>
                                <main-table :config="config" :detalle="detalle" :table_id="'datos_adicionales_table'"></main-table>
                                <modal :modal="config.modal"></modal>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
    	</div><!-- cierra .col-lg-12 -->

    </div><!-- cierra #page-wrapper -->

</div><!-- cierra #wrapper -->
