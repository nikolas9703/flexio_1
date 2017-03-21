<div id="wrapper">

    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

	    <div class="wrapper wrapper-content">
	    <!-- CONTENT WRAPPER -->

	    	<div id="mensaje_info"></div>
	    	
	    	<?php
			$formAttr = array(
		        'method'       => 'POST',
		        'id'           => 'configuracionReportesForm',
		        'autocomplete' => 'off'
            );

             echo form_open_multipart("", $formAttr);
             ?>
	    	<div class="panel-group" aria-multiselectable="true" role="tablist">

	    		<div class="panel panel-blanco" id="accordeonCatalogos">
					<div class="panel-heading panel-blanco-heading">
						<h5 class="panel-title">
							<a class="" data-toggle="collapse" data-parent="#administrador_de_actividades" href="#collapse-administrador_de_actividades" aria-expanded="true">Reportes</a>
						</h5>
					</div>
					<div style="" id="collapse-administrador_de_actividades" class="panel-collapse collapse in" aria-expanded="true">
						<div class="panel-body">


							<div class="table-responsive">
							   <table class="table table-noline tabla-dinamica" id="reportesTable">
							      <thead>
							         <tr>
							            <th>Nombre del Reporte <span required="" aria-required="true">*</span></th>
							            <th>Activo</th>
							            <th>Fecha <span required="" aria-required="true">*</span></th>
							            <th>Rol <span required="" aria-required="true">*</span></th>
							            <th>Usuario</th>
							            <th colspan="2">&nbsp;</th>
							         </tr>
							      </thead>
							      <tbody>
							         <tr id="0">
							            <td width="30%">
							            	<select id="id_reporte0" name="reporte[0][id_reporte]" class="form-control chosen-select" data-rule-required="true" data-msg-required="Debe llenar todos los campos marcados con *.">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($reportes))
		                                        {
		                                            foreach ($reportes AS $reporte) {
		                                                echo '<option value="'. $reporte['id'] .'">'. $reporte['descripcion'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
							            </td>
							            <td><div class="checkbox checkbox-success" align="center"><input type="checkbox" id="activo0" name="reporte[0][activo]" value="" class="form-control notificaciones-checkbox" /><label for="activo0">&nbsp;</label></div></td>
							            <td><input type="text" id="fecha_ejecucion0" name="reporte[0][fecha_ejecucion]" value="" class="form-control daterange-picker" data-rule-required="true" data-msg-required="Debe llenar todos los campos marcados con *." /></td>
							            <td>
							            	<select id="id_rol0" name="reporte[0][id_rol]" class="form-control chosen-select role-change" data-rule-required="true" data-msg-required="Debe llenar todos los campos marcados con *.">
												<option value="" selected="selected">Seleccione</option>
		                                        <?php
		                                        if(!empty($roles))
		                                        {
		                                            foreach ($roles AS $role) {
		                                                echo '<option value="'. $role['id_rol'] .'">'. $role['nombre_rol'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
							            </td>
							            <td width="20%">
							            	<select id="id_usuario0" name="reporte[0][id_usuario][]" class="form-control chosen-select chosen-usuarios hasUsuarios" multiple="multiple" size="1" data-placeholder="No hay usuarios">
												<option value="" class="hide">Seleccione</option>
		                                    </select>
							            </td>
							            <td><button class="btn btn-default btn-block eliminarBtn" type="button"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;Eliminar</span></button></td>
							            <!--<td><button id="agregarBtn" class="btn btn-default btn-block" type="button" name=""><i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;Agregar</span></button></td>-->
							         </tr>
							      	</tbody>
								  	<tfoot>
										<tr>
								   			<td class="formerror"></td>
								   		</tr>
								  	</tfoot>
							   </table>

							   <input type="hidden" name="tipo" value="reporte" />
							</div>

							<div class="row">
								<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a id="cancelarContacto" class="btn btn-default btn-block" href="#">Cancelar</a> </div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><input type="button" id="guardarReporteBtn" class="btn btn-primary btn-block" value="Guardar" name="campo[guardarPrincipal]"></div>
							</div>

						</div>
					</div>
				</div>

	    	</div>
			<?php echo form_close(); ?>

		<!-- /END CONTENT WRAPPER -->
		</div>

	</div>
</div>

<?php echo Modal::modalOpciones(); ?>
