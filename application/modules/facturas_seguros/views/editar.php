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

                <div class="loader"><span class="h5 font-bold block"><i class="fa fa-cog fa-spin fa-fw"></i> Cargando...</span></div>

                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'facturaForm',
                    'autocomplete' => 'off',
                    'class' => 'hide animated white-bg'
                );
                echo form_open(base_url('facturas_seguros/guardar_cambios'), $formAttr);
                ?>

                <!-- Componentes -->
                <div class="row">
                    <filtro_factura v-ref:filtro></filtro_factura>
                    <datos_factura v-ref:datos_factura></datos_factura>
                </div>

                <!-- Botones -->
                <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 white-bg">
                    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <a href="<?php echo base_url('facturas_seguros/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
                    </div>
                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                        <input type="button" id="guardarBtn" name="guardar" class="btn btn-primary btn-block" value="Guardar" @click.stop.prevent="guardar" />
                    </div>
                </div>

                <?php
                //cargar templates vue
                echo modules::run('facturas_seguros/vue_cargar_templates');

                //inicializar modal
                echo Modal::config(array(
                    "id" => "opcionesModal",
                    "size" => "sm",
                    "titulo" => "{{{modal.titulo}}}",
                    "contenido" => "{{{modal.contenido}}}",
                    "footer" => "{{{modal.footer}}}",
                ))->html();
                echo form_close();
                ?>

            </div>

            <div id="form_crear_facturas_div">
            <?php if (!empty($uuid)): ?>
                <!-- Subpaneles -->
                <div class="subpaneles hide col-lg-12 col-md-12 col-xs-12 m-t-md white-bg">
                    <?php SubpanelTabs::visualizar($subpanels); ?>
                </div>

                <!-- Comentarios -->

                <!--<div class="row">
                     <vista_comments
                      v-if="config.vista === 'editar'"
                      :config="config"
                      :historial.sync="comentario.comentarios"
                      :modelo="comentario.comentable_type"
                      :registro_id="comentario.comentable_id"
                      ></vista_comments>
                    </div>-->
                
                <!-- Comentarios -->
            <?php endif; ?>
            </div>

        </div>
        <!-- cierra .col-lg-12 -->
    </div>
    <!-- cierra #page-wrapper -->
</div>
<?php
echo Modal::config(array(
	"id" => "documentosModalEditar",
	"size" => "md",
	"titulo" => "Cambiar nombre del documento",
	"contenido" => modules::run("facturas_seguros/formularioModalEditar")
))->html();
?>
<?php

$formAttr = array('method' => 'POST', 'id' => 'exportarDocumentos','autocomplete'  => 'off');
echo form_open(base_url('documentos/exportarDocumentos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_documentos" value="" />
<input type="hidden" name="solicitud" id="solicitud_exp" value="" />
<?php
echo form_close();

?>
