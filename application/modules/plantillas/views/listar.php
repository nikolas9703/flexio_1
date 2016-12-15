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
                <div ng-controller="toastController"></div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar plantilla</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarPlantillasForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Tipo de plantilla</label>
                                            <select class="form-control chosen-select" id="plantilla_id">
                                                <option value="">Seleccione</option>
                                                <?php
                                                 $i=1;                                  
                                               foreach($plantillas AS $grupo => $plantillass){
								foreach($plantillass AS $plantilla){
                                                                     echo '<option value="' . $grupo."-".$plantilla['nombre']. '">' .$grupo." - ".$plantilla['nombre']. '</option>';
                                                                     $i++;
								}
                                                        $i++;
						}
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Fecha de creaci&oacute;n</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                      <input type="text" name="desde" id="fecha1" class="form-control">
                                                      <span class="input-group-addon">a</span>
                                                      <input type="text" class="form-control" name="hasta" id="fecha2">
                                                    </div>
                                                </div>
                                            </div>
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
<?php echo modules::run('plantillas/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
//Formulario Exportar
$formAttr = array('method' => 'POST', 'id' => 'exportarPlantillaForm', 'autocomplete' => 'off');
echo form_open(base_url('plantillas/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

<?php
//Redirigir Formulario Crear
$formAttr = array('method' => 'POST', 'id' => 'crearPlantillaForm', 'autocomplete' => 'off');
echo form_open(base_url('plantillas/crear'), $formAttr);
echo form_close();

//Modal Opciones
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();

//Modal Opciones Crear
echo Modal::config(array(
    "id" => "opcionesCrearModal",
    "size" => "sm"
))->html();
?>


