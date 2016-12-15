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
									<h5>Buscar Aseguradoras</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
							     	
							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarAseguradoraForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Nombre Aseguradora</label>
							            	<input type="text" id="nombre" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">RUC</label>
							            	<input type="text" id="ruc" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Teléfono</label>
							            	<input type="text" id="telefono" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Correo Electrónico</label>
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
									<?php echo form_close(); ?>
									
								<!-- Termina campos de Busqueda -->
								</div>
							</div>
							<!-- /BUSCADOR -->
		
				    	
				    		<!-- JQGRID -->
				    		<?php echo modules::run('aseguradoras/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>
				  	</div>
				</div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<div id="menu_crear" style="display:none;">
<?php foreach($menu_crear AS $row){ 
    $id_solicitudes = $row['id'];  
?>
<?php if($row['padre_id'] == 0 && $row['estado'] == 1 && $row['level'] == 1){ ?>
<a href="#collapse0000<?php echo $row['id'] ?>" class="btn btn-block btn-outline btn-success" style="margin-bottom:5px;" data-toggle="collapse" data-id="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></a>
<div id="collapse0000<?php echo $row['id'] ?>" class="collapse">
<ul id="<?php echo $row['nombre'] ?>" class="list-group clear-list">      
<?php foreach($menu_crear AS $info){  if($info['padre_id'] == $id_solicitudes && $info['estado'] == 1 && $info['is_padre'] == 1){ ?>
<li class="m-sm">
<a href="#collapse0000<?php echo $info['id'] ?>" data-toggle="collapse" data-id="<?php echo $info['id'] ?>"><?php echo $info['nombre'] ?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
</li>
<div id="collapse0000<?php echo $info['id'] ?>" class="collapse">
<ul id="<?php echo $info['nombre'] ?>" class="list-group clear-list">
<?php foreach($menu_crear AS $result){  if($result['padre_id'] == $info['id'] && $result['estado'] == 1 && $result['level'] == 3 && !empty($result['codigo_ramo'])){ ?>    
<li class="m-md">     <a id="botonAccionar" href="#" data-formulario="<?php echo $result['id'] ?>"><?php echo $result['nombre'] ?></a></li>
<?php } } ?>
</ul>
</div>
<?php  } } ?>
</ul>
</div>
<?php } } ?>
</div>
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarColaboradores','autocomplete'  => 'off');
echo form_open(base_url('colaboradores/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

<?php 
$formAttr = array('method' => 'POST', 'id' => 'SolicitudesForm','autocomplete'  => 'off');
echo form_open(base_url('solicitudes/crear'), $formAttr);
echo '<input type="text" id="solicitudes_id" name="solicitud_id" />';
echo form_close(); 
echo Modal::config(array(
"id" => "opcionesModal",  
"size" => "sm"
))->html();
echo Modal::config(array(
"id" => "moduloOpciones",  
"size" => "sm"
))->html();
?>
