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
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>
	            
	            <div class="row" ng-controller="identificacionController">
                        <div class="tab-content">
                            <div id="datosgeneralesdelproveedor-43" class="tab-pane active">
                                <?php 
                                    $aux = [
                                        "method"        => "POST",
                                        "id"            => "crearAcreedoresForm",
                                        "autocomplete"  => "off"
                                    ];
                                    echo form_open(base_url("acreedores/guardar"), $aux);
                                ?>
                                <div class="ibox">
                                    <div class="ibox-title border-bottom">
                                        <h5>Datos generales del acreedor</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </div>
                                    </div>
                                    <?php echo modules::run('acreedores/ocultoformulario', $campos); ?>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                    
                    <div id="sub-panel" class="row">
                        <div id="sub-panel-formulario-modulos" class="tab-content">
                        </div>
                        <div id="sub-panel-grid-modulos">
                            <div class="panel-heading white-bg" style="height:70px !important">
    				<span class="panel-title">&nbsp;</span>
                                <ul class="nav nav-tabs nav-tabs-xs" role="tablist">
                                    <li class="dropdown active">
                                        <a href="#tablaColaboradores" data-toggle="tab" aria-controls="tablaColaboradores" role="tab">
                                            <span class="fa fa-users"></span> Colaboradores
                                        </a>
                                        <ul class="dropdown-menu sub-panel-dropdown-contenido hide" role="menu">
                                            <li class="active">
                                                <a href="#tablaColaboradores" data-toggle="tab" data-targe="#tablaColaboradores">Tabla</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content white-bg" style="margin-top: -18px;">
                                <div role="tabpanel" class="tab-pane active" id="tablaColaboradores">
                                    <?php echo modules::run('acreedores/ocultotablaColaboradores'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <br/><br/>
                <?php echo modules::run('acreedores/ocultoformulariocomentarios'); ?>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 

    echo    Modal::config(array(
                "id"    => "optionsModal",
                "size"  => "sm"
            ))->html();

?>