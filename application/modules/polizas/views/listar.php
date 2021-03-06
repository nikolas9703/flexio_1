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
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
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
                                    <h5>Buscar Póliza</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarPolizaForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Cliente</label>
                                            <input type="text" id="cliente" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Aseguradora</label>
                                            <select id="aseguradora" class="form-control chosen">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($aseguradoras)) {
                                                    foreach ($aseguradoras AS $aseguradora) {
                                                        echo '<option value="' . $aseguradora->id . '">' . $aseguradora->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Ramo</label>
                                            <select id="ramo" class="form-control" multiple="multiple">
                                                <?php
                                                if (!empty($ramos)) {
                                                    foreach ($ramos AS $ramo) {
                                                        if ($ramo->padre_id != 0 && in_array($ramo->id, $rolesArray) && in_array($ramo->id, $usuariosArray)) {
                                                        echo '<option value="' . $ramo->nombre . '">' . $ramo->nombre . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. Póliza</label>
                                            <input type="text" id="no_poliza" class="form-control" value="" placeholder="">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Vigencia</label>
                                            <div class="input-group">
                                                <input type="text" id="inicio_vigencia" class="form-control" value="" placeholder="">
                                                <span class="input-group-addon">a</span>
                                                <input type="text" id="fin_vigencia" class="form-control" value="" placeholder="">
                                            </div>
                                        </div>
                                        <!--<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Usuario</label>
                                            <select id="usuario" class="form-control">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($usuarios)) {
                                                    foreach ($usuarios AS $usuario) {
                                                        echo '<option value="' . $usuario->id . '">' . $usuario->nombre . ' ' . $usuario->apellido . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>-->

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Categoria</label>
                                            <select id="categoria" class="form-control chosen">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($categorias)) {
                                                    foreach ($categorias AS $categoria) {
                                                        echo '<option value="' . $categoria->id . '">' . $categoria->valor . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Declarativa</label>
                                            <select id="declarativa" class="form-control">
                                                <option value="">Seleccione</option>
                                                <option value="si">Si</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select id="estado" class="form-control chosen">
                                                <option value="">Seleccione</option>
                                                <?php
                                                if (!empty($estados)) {
                                                    foreach ($estados AS $estado) {
                                                        echo '<option value="' . $estado->valor . '">' . $estado->valor . '</option>';
                                                    }
                                                }
                                                ?>
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
                                    <?php echo form_close(); ?>

                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->

                            <!-- Opcion: Mostrar/Ocultar columnas del jQgrid -->
                            <div id="jqgrid-column-togle" class="row"></div>

                            <!-- Listado de Clientes -->

                            <div class="NoRecordsEmpresa text-center lead"></div>

                            <!-- the grid table -->
                            <table class="table table-striped" id="PolizasGrid"></table>

                            <!-- pager definition -->
                            <div id="pager_polizas"></div>

                            <!-- /Listado de Clientes -->

                            <!-- /JQGRID -->
                        </div>

                        <div role="tabpanel" class="tab-pane" id="grid">
                            <?php //Grid::visualizar_grid($grid); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarPolizas', 'autocomplete' => 'off');
echo form_open(base_url('polizas/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();
?>
<?php 
$formAttr = array('method' => 'POST', 'id' => 'ReclamosForm', 'autocomplete' => 'off');
echo form_open(base_url('reclamos/crear'), $formAttr);
echo '<input type="text" id="ramo_id" name="ramo_id" />';
echo '<input type="text" id="poliza_id" name="poliza_id" />';
echo form_close();
 ?>

<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalCambioEstado",
    "size" => "sm"
))->html();
echo Modal::config(array(
    "id" => "documentosModal",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("Polizas/formularioModal")
))->html();
?>

<?php
echo Modal::config(array(
    "id" => "opcionesModalRenovation",
    "size" => "sm",
    "contenido" => modules::run('Polizas/renovationView')
))->html();
?>