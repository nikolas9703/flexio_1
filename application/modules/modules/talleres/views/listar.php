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
                    <?php $mensaje = self::$ci->session->flashdata('mensaje'); ?>
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>
                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Equipo de trabajo</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <?php
		                            $formAttr = array(
		                                'method' => 'POST',
		                                'id' => 'buscarEquiposTalleresForm',
		                                'autocomplete' => 'off'
		                            );
		
		                            echo form_open_multipart("", $formAttr);
		                            ?>
                                    <div class="row ">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. de Equipo</label>
                                            <input type="text" id="codigo" class="form-control" value="" placeholder="">
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Nombre de Equipo</label>
                                            <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="estado_id">Estado</label>
                                            <select id="estado_id" class="form-control">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($estados as $estado): ?>
                                                    <option value="<?php echo $estado["id"] ?>"><?php echo $estado["nombre"]; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
                                        </div>
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                    <?php echo form_close(); ?>
                                    
                                </div>
                            </div>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('talleres/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarEquipoTalleres', 'autocomplete' => 'off');
echo form_open(base_url('talleres/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value=""/>
<?php echo form_close(); ?>
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();
?> <!-- modal opciones -->
