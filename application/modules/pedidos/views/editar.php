<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="form_crear_pedido_div">
                <div class="row">
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>

                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'form_crear_pedido',
                    'autocomplete' => 'off'
                );

                echo form_open(base_url('pedidos/guardar'), $formAttr);
                ?>

                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Datos generales del pedido</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <?php
                    echo modules::run('pedidos/ocultoformulario',$pedido_obj);
                    ?>

                </div>
                <?php echo form_close(); ?>

                <div class="row">
                    <?php SubpanelTabs::visualizar("sp_pedido_id=$pedido_id"); ?>
                </div>
                <!-- Comentarios -->
                 <div class="row">
                     <vista_comments
                      v-if="config.vista === 'editar'"
                      :config="config"
                      :historial.sync="comentario.comentarios"
                      :modelo="comentario.comentable_type"
                      :registro_id="comentario.comentable_id"
                      ></vista_comments>
                    </div>
                 <!-- Comentarios -->
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
))->html();
