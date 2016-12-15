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
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Cliente</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                            <label for="">Nombre de Cliente</label>
                                            <input type="text" id="nombre_cliente" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                            <label for="">Telef&oacute;no</label>
                                            <input type="text" id="telefono" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                            <label for="">Correo Electr&oacute;nico</label>
                                            <input type="text" id="email" class="form-control" value="" placeholder="">
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
                                </div>
                            </div>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <!-- Llama al modulo y el metodo indicado -->
                            <?php echo modules::run('Grupo_clientes/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                        <div role="tabpanel" class="tab-pane" id="grid">
                            <?php //Grid::visualizar_grid($vars); ?>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php
$formAttrs = array('method' => 'POST', 'id' => 'exportarGrupoClientes', 'autocomplete' => 'off');
echo form_open(base_url('grupo_clientes/exportar'), $formAttrs);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php
$formAttrss = array('method' => 'POST', 'id' => 'editarGrupoClientes', 'autocomplete' => 'off');
echo form_open(base_url('grupo_clientes/ver'), $formAttrss);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php

$formAttr = array(
    'method' => 'POST',
    'id' => 'crearGrupoClienteForm',
    'autocomplete' => 'off'
);
echo Modal::config(array(
    "id" => "modalCrearGrupoCliente",
    "titulo" => "Crear: Grupo de Cliente",
    "contenido" => modules::run('grupo_clientes/ocultoformulario'),
    "size" => "md",
    "footer" => '<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-0 col-sm-0 col-md-4 col-lg-4">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <input type="button" id="cancelarBtn" class="btn btn-default btn-block" value="Cancelar" />
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <input type="button" id="guardarBtn" class="btn btn-primary btn-block" value="Guardar" />
    </div>
  </div>'
))->html();
?>
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();?>

