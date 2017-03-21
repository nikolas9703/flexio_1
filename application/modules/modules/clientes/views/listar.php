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
	            
				<div role="tabpanel">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">

							<!-- BUSCADOR -->
							<?php
                            $formAttr = array(
	                            'method'       => 'POST',
	                            'id'           => 'buscarClientesForm',
	                            'autocomplete' => 'off'
                            );

                            echo form_open_multipart("", $formAttr);
                            ?>
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
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Nombre de Cliente</label>
							            	<input type="text" id="nombre_cliente" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Identificaci&oacute;n</label>
											<select name="campo[identificacion]" class="form-control" id="identificacion">
												<option value="">Seleccione</option>
												<?php foreach ($info['identificacion'] as $identificacion) { ?>
													<option value="<?php if($identificacion->etiqueta=='natural'){ echo 'cedula'; }else if($identificacion->etiqueta=='juridico'){ echo 'ruc'; }  ?>"><?php echo $identificacion->valor ?></option>
												<?php } ?>
												<option value="pasaporte">Otro</option>
											</select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Tel&eacute;fono</label>
							            	<!-- <input type="text" id="telefono" class="form-control" value="" placeholder="">-->
                            <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                            <input type="hidden" value="">
                            <input type="input-left-addon" class="form-control" id="telefono" >
                            </div>

										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Correo Electr&oacute;nico</label>
									      <!--  <input type="text" id="email" class="form-control" value="" placeholder="">-->
                          <div class="input-group">
                          <span class="input-group-addon">@</span>
                          <input type="hidden" value="">
                          <input type="input-left-addon" class="form-control" id="email" >
                          </div>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Tipo de cliente</label>
											<select name="campo[tipo]" class="form-control" id="tipo">
												<option value="">Seleccione</option>
												<?php foreach ($info['tipo'] as $tipo) { ?>
													<option value="<?php echo $tipo->id ?>"><?php echo $tipo->nombre ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Categor&iacute;a de cliente</label>
                                            <select name="campo[categoria]" class="form-control" id="categoria">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($info['categoria'] as $categoria) { ?>
                                                    <option value="<?php echo $categoria->id ?>"><?php echo $categoria->nombre ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Estado</label>
											<select name="campo[estado]" class="form-control" id="estado">
												<option value="">Seleccione</option>
												<?php foreach ($info['estado'] as $estado) { ?>
													<option value="<?php echo $estado->etiqueta ?>"><?php echo $estado->valor ?></option>
												<?php } ?>
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
							<?php echo form_close(); ?>
							<!-- /BUSCADOR -->

				    		<!-- JQGRID -->
				    		<?php echo modules::run('clientes/ocultotabla'); ?>

				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttrx = array('method' => 'POST', 'id' => 'exportarClientes', 'autocomplete' => 'off');
echo form_open(base_url('clientes/exportar'), $formAttrx);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
    <?php
$formAttrs = array(
    'method' => 'POST',
    'id' => 'crearAgrupadorClienteForm',
    'autocomplete' => 'off'
);

echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();
echo Modal::config(array(
    "id" => "modalAgrupadorCliente",
    "titulo" => "Agrupar",
    "contenido" => modules::run('clientes/ocultoformularioagrupador'),
    "size" => "sm",
    "footer" => '<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <input type="button" id="cancelarBtn" class="btn btn-default btn-block" value="Cancelar" />
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <input type="button" id="guardarBtn" class="btn btn-primary btn-block" value="Guardar" />
    </div>
  </div>'
))->html();

$formAttr = array('method' => 'POST', 'id' => 'crearCotizacionesForm','autocomplete'  => 'off');
echo form_open(base_url('cotizaciones/crear'), $formAttr);
echo form_close();

?>

<?php echo Modal::config(array( "id" => "optionsModal", "size"  => "sm"))->html();?> <!-- modal opciones -->
<?php //echo Modal::modalSubirDocumentos();?>  <!-- modal subir documentos -->
