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
				<div class="row" ng-controller="AjustadoresFormularioController">
					<?php 
						echo modules::run('ajustadores/ocultoformulario');
					?>
				</div>
                            <div class="row" id="sub-panel" >
					<div style="height:50px !important" class="panel-heading white-bg">	
			    		<ul class="nav nav-tabs nav-tabs-xs">
							<li class="active"><a role="tab" data-toggle="tab" href="#accionPersonalTabla">Contactos</a></li>
						</ul>
					</div>
					<div class="tab-content white-bg p-xs">
						<div id="contactoTabla" class="tab-pane active" role="tabpanel">
                                                    <!-- Inicia campos de Busqueda -->
					     	<?php
	                        $formAttr = array(
	                            'method'        => 'POST', 
	                            'id'            => 'buscarContactosForm',
	                            'autocomplete'  => 'off'
	                          );
	                         echo form_open(base_url(uri_string()), $formAttr);
	                        ?>
					     	<div class="row">
					        	<div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
					            	<label for="">Nombre</label>
					            	<input type="text" id="nombre" class="form-control" value="" />

								</div>
                                                    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
					            	<label for="">Cargo</label>
					            	<input type="text" id="cargo" class="form-control" value="" />

								</div>
                                                    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
					            	<label for="">E-mail</label>
					            	<input type="text" id="correo" class="form-control" value="" />

								</div>
                                                    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
					            	<label for="">Celular</label>
					            	<input type="text" id="celular" class="form-control" value="" />

								</div>
                                                    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
					            	<label for="">Tel&eacute;fono</label>
					            	<input type="text" id="telefono" class="form-control" value="" />

								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
					            	<label for="">&Uacute;ltimo contacto</label>
					            	<div class="input-group">
						    			<input type="text" id="ultimo_contacto_desde" readonly="readonly" class="form-control" value="" />
										<span class="input-group-addon">a</span>
										<input type="text" id="ultimo_contacto_hasta" readonly="readonly" class="form-control" value="" />
						    		</div>
								</div>
								
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
									<label for="">&nbsp;</label>
									<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
								</div>
							</div>
							<?php echo form_close(); ?>
							<!-- Termina campos de Busqueda -->
						<?php echo modules::run('ajustadores/ocultotablacontacto', ""); ?>
						</div>
						
					</div>
				</div>
			</div>

		</div><!-- cierra .col-lg-12 -->
                
	</div><!-- cierra #page-wrapper -->
        
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarContactos','autocomplete'  => 'off');
echo form_open(base_url('ajustadores/exportar_contactos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php

echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
))->html();

?>