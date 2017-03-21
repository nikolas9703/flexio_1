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
                                    <h5>Buscar Acreedor</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row" id="buscarAcreedoresForm">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="nombre">Nombre de acreedor</label>
                                            <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="tipo">Tipo</label><br>
                                            <select id="tipo" class="form-control chosen">
                                                <option value="">Seleccione</option>
                                                <?php foreach($tipos as $tipo):?>
                                                <option value="<?php echo $tipo->id_cat?>"><?php echo $tipo->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="telefono">Tel&eacute;fono</label>
                                            <input type="text" id="telefono" class="form-control" value="" placeholder="">
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
                            <?php echo modules::run('acreedores/ocultotabla'); ?>
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
//formulario exportar
$formAttr = array('method' => 'POST', 'id' => 'exportar','autocomplete'  => 'off');
echo form_open(base_url('acreedores/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close();

//modal opciones
echo Modal::config(array(
"id"    => "optionsModal",
"size"  => "sm"
))->html();
?>
