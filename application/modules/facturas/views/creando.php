<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div id="formulario_factura_venta" class="wrapper-content">
                <div class="row">
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["mensaje"] : ''  ?>
	                </div>
	            </div>
                <div class="row loader" v-if="config.loading"><span class="h5 font-bold block"><i class="fa fa-cog fa-spin fa-fw"></i> Cargando...</span></div>

                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'facturaForm',
                    'autocomplete' => 'off',
                    'class' => 'vue-formulario animated fadeIn'
                );
                echo form_open(base_url('facturas/guardar'), $formAttr);
                ?>

                <div class="row" v-show="config.acceso && !config.loading">

                    <!--componente empezar desde-->
                    <empezar-desde :info="config" :empezable="empezable"></empezar-desde>
                    <!--componente empezar desde-->

                    <!--componente formulario-->
                    <formulario :catalogos="catalogoFormulario" :factura="factura" :config="config" :campo-disabled="campoDisabled.estadoDisabled"></formulario>
                    <!--componente formulario-->

                    <!--componente dinamico para alquiler   -->
                    <component :is="tablaAlquilerActual" :articulos-alquiler="articulos_alquiler" v-if="tablaActual=='tabla-articulos-alquiler'"></component>
                    <!--componente dinamico para alquiler -->

                    <!--componente dinamico   -->
                    <component :is="tablaActual" :tabla-header="tablaActual" :config="config" :articulos="articulos" :catalogos="catalogoFormulario.catalogoItems" :campo-disabled="campoDisabled.estadoDisabled"></component>
                    <!--componente dinamico  -->

                    <!--componente totales  -->
                    <totales :lista-articulos="articulos" :lista-articulos-alquiler="articulos_alquiler"></totales>
                    <!--componente totales  -->

                    <!--componente botones  -->
                    <formulario-botones :config="config"></formulario-botones>
                    <!--componente botones  -->

                </div>
                <?php
                //cargar templates vue
                //echo modules::run('facturas/vue_cargar_templates');
                //inicializar modal
                /*echo Modal::config(array(
                    "id" => "opcionesModal",
                    "size" => "sm",
                    "titulo" => "{{{modal.titulo}}}",
                    "contenido" => "{{{modal.contenido}}}",
                    "footer" => "{{{modal.footer}}}",
                ))->html();*/
                echo form_close();
                ?>
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
        </div>
        <!-- cierra .col-lg-12 -->
    </div>
    <!-- cierra #page-wrapper -->
</div>
<!-- cierra #wrapper -->
