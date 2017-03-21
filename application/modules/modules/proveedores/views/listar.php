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
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
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
                                    <h5>Buscar Proveedor</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="categoria">Categor&iacute;a(s)</label><br>
                                            <select id="categoria" class="form-control" data-placeholder="Seleccione" multiple="">
                                                <?php
                                                if(!empty($info['categorias'])):
                                                  foreach ($info['categorias'] as $categoria) { ?>
                                                      <option value="<?php echo $categoria->id?>" <?php echo ((isset($info['catSelect'])) && in_array($categoria->id, $info['catSelect'])) ? ' selected ' : ''?>><?php echo $categoria->nombre;?></option>
                                                  <?php }
                                                endif;
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="tipo">Tipo</label><br>
                                            <select id="tipo" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php if(!empty($tipos)): foreach($tipos as $tipo):?>
                                                <option value="<?php echo $tipo["id"]?>"><?php echo $tipo["nombre"]?></option>
                                              <?php endforeach; endif;?>
                                            </select>
                                        </div>
                                    <!-- ********************************************************************************************* -->
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="tipo">Estado</label><br>
                                            <select id="estados" class="form-control" data-placeholder="Seleccione">
                                                <option value=""> </option>
                                                <?php if(!empty($estados)): foreach($estados as $row_estados):?>
                                                <option value="<?php echo $row_estados["valor"]?>"><?php echo $row_estados["etiqueta"] ?></option>
                                              <?php endforeach; endif;?>
                                            </select>
                                        </div>
                                    <!-- ********************************************************************************************* -->
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
                            <?php echo modules::run('proveedores/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                        <div role="tabpanel" class="tab-pane" id="grid">
                            <?php
                                //QUITO ESTOS ELEMENTOS DEL ARRAY
                                //PARA EVITAR CONFLICTOS DE INDICES INDEFINIDOS
                                unset($vars["tipos"]);
                                unset($vars["categorias"]);
                            ?>
                            <?php //Grid::visualizar_grid($vars); ?>
                        </div>

                    </div>
                </div>
            </div>

    	</div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php
echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();

    echo    Modal::config(array(
                "id"    => "optionsModal",
                "size"  => "sm"
            ))->html();

$formAttr = array('method' => 'POST', 'id' => 'crearEstadoProveedor','autocomplete'  => 'off');
echo form_open(base_url('reportes_financieros/reporte/estado_cuenta_proveedor'), $formAttr);
echo form_close();

?>
