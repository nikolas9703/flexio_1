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

				<div class="row editarFormularioClientesNaturales">
                	<?php Template::cargar_formulario($clientes); ?>


					<!-- porcentaje completado -->
					<div class="ibox-content">
						<div class="row">
							<!-- CONTENIDO -->
							<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-1 tab-pane">
								<h4>Completo</h4>
							</div>
							<div class="form-group col-xs-12 col-sm-10 col-md-10 col-lg-11 tab-pane">
								<div class="progress progress-bar-default" style="margin-bottom:5px;">
									<div class="progress-bar" role="progressbar" aria-valuenow="93" aria-valuemin="0" aria-valuemax="100" style="background-color: #0751AA;width: <?php echo !empty($porcentaje_completado) ? $porcentaje_completado : 0; ?>%">
										<span class="sr-only"><?php echo !empty($porcentaje_completado_natural) ? $porcentaje_completado_natural : 0; ?>% Completado</span>
									</div>
								</div>
								<p>Datos del Cliente completados en <?php echo !empty($porcentaje_completado) ? $porcentaje_completado : 0; ?>%</p>
			           		</div>
			           		<!-- /CONTENIDO -->
		           		</div>
		           	</div>
		           	<!-- /porcentaje completado -->
		           	<div>&nbsp;</div>
                </div>
                <?php
 				Subpanel::visualizar_grupo_subpanel($id_cliente); ?>
        	</div>

					<!-- Comentarios -->
	                 <div class="row" id="form_crear_cliente_div">
	                     <vista_comments
	                      v-if="config.vista === 'editar'"
	                      :config="config"
	                      :historial.sync="comentario.comentarios"
	                      :modelo="comentario.comentable_type"
	                      :registro_id="comentario.comentable_id"
	                      ></vista_comments>
	                    </div>
	                 <!-- Comentarios -->

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<input value="clientes" id="controlador" type="hidden"/>
<?php echo Modal::modalOpciones();?> <!-- modal opciones -->
<?php echo Modal::modalSubirDocumentos();?> <!-- modal subir documentos -->
<?php echo Util::actulizarArchivosDocumento($modulos); ?>
