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
                                    <h5>Buscar cajas</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarCajasForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
                                    <div class="row ">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" id="nombre" class="form-control" value="">
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="centro">Centro Contable</label>
                                            <select id="centro" class="form-control">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($centros as $centro): ?>
                                                    <option value="<?php echo $centro['centro_contable_id'] ?>"><?php echo $centro['nombre'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="limite">L&iacute;mite</label>
                                            <div class="input-group">
	                                            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
	                                            <input type="text" value="" id="limite" class="form-control" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" />   
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="responsable_id">Responsable</label>
                                            <select id="responsable_id" class="form-control">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                    <option value="<?php echo $usuario->id ?>"><?php echo $usuario->nombre ." ". $usuario->apellido ?></option>
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
                            <?php echo modules::run('cajas/ocultotabla'); ?>
                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
$formAttrs = array('method' => 'POST', 'id' => 'transferirForm', 'autocomplete' => 'off');
echo form_open(base_url('cajas/transferir'), $formAttrs);
?>
<input type="hidden" name="id" id="id" value="" />

<?php echo form_close(); ?>
<?php
echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();
?>
<?php 
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>

