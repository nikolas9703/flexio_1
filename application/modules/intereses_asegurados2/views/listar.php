<style>#load_tablaInteresesAseguradosGrid{background: rgb(0, 112, 186) none repeat scroll 0% 0% !important;}</style>
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
                                    <h5>Buscar Intereses Asegurados</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row" id="buscarInteresesAseguradosForm">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="numero">No. de interés asegurado</label>
                                            <input type="text" id="numero" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="tipo">Tipo de interés asegurado</label><br>
                                            <select id="tipo" class="form-control chosen">
                                                <option value="">Seleccione</option>
                                                <?php foreach($tipos_intereses_asegurados as $tipo):?>
                                                <option value="<?php echo $tipo->id_cat?>"><?php echo $tipo->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label id="identificacion_label" for="asegurado">Identificacion</label>
                                            <input type="text" id="identificacion" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="estado">Estado</label><br>
                                            <select id="estado" class="form-control chosen">
                                                <option value="">Seleccione</option>
                                                <?php foreach($estado as $row): ?>
                                                <option value="<?php echo $row->id_cat?>"><?php echo $row->etiqueta?></option>
                                                <?php endforeach;?>
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
                                </div>
                            </div>
                            <!-- /BUSCADOR -->
				    	
                            <!-- JQGRID -->
                            <?php echo modules::run('intereses_asegurados/ocultotabla'); ?>
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

<!-- inicia #crearModal -->
<div class="modal fade bs-example-modal-sm" id="crearModal" tabindex="-1" role="dialog" aria-labelledby="crearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Interés Asegurado: Crear</h4>
            </div>


            <div class="modal-body">
                <?php foreach ($tipos_intereses_asegurados as $tipo):?>
                    <a href="<?php echo base_url("intereses_asegurados/crear/".$tipo->valor); ?>" id="" class="btn btn-block btn-outline btn-success"><?php echo $tipo->etiqueta; ?></a>
                <?php endforeach;?>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- termina #optionsModal -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarIntereses','autocomplete'  => 'off');
echo form_open(base_url('intereses_asegurados/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

<?php 
echo    Modal::config(array(
    "id"    => "optionsModal",
    "size"  => "sm"
))->html();

/*echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();
*/


