<div id="vistaCliente" class="">
	<div class="tab-content">
		<?php  
			$formAttr = array(
				'method' => 'POST',
				'id' => 'formAseguradoraCrear',
				'autocomplete' => 'off'
			);

			if($campos['uuid_aseguradora']!="") 
			{
				echo form_open(base_url('aseguradoras/editar'), $formAttr);
			}
			else
			{
				echo form_open(base_url('aseguradoras/guardar'), $formAttr);
			}
			$disabled="";
			if($campos['guardar']==0)
					$disabled='disabled="disabled"';
		?>
		
		<div id="datosdelaaseguradora-5" class="col-lg-12">
			<input type="hidden" name="campo[uuid]" id="campo[uuid]" value="<?php echo $campos['uuid_aseguradora']?>" />
			<input type="hidden" name="campo[uuid_aseguradora]" id="campo[uuid_aseguradora]" value="<?php echo $campos['uuid_aseguradora']?>" />
			<div class="ibox">  
				<div class="ibox-title">
                        <h5>Datos de la aseguradora</h5>
                </div>
				<div class="ibox-content " style="display: block;" id="datosGenerales" >
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6  ">
							<label>Nombre aseguradora <span required="" aria-required="true">*</span></label>
							<input type="text" name="campo[nombre]" class="form-control nombre" id="campo[nombre]" data-rule-required="true" value="<?php echo $campos['nombre']?>" <?php echo $disabled?> />
							
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Teléfono </label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-phone"></i></span>
								<input type="input-left-addon" name="campo[telefono]" class="form-control telefono" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]" value="<?php echo $campos['telefono']?>" <?php echo $disabled?> />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Correo electrónico </label>
							<div class="input-group">
								<span class="input-group-addon">@</span>
								<input type="input-left-addon" name="campo[email]" data-addon-text="@" class="form-control email"  id="campo[email]" value="<?php echo $campos['email']?>" <?php echo $disabled?> />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Tomo/Rollo</label>
							<input type="text" name="campo[tomo]" class="form-control" id="campo[tomo]" value="<?php echo $campos['tomo']?>" <?php echo $disabled?> />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
							<label>Folio/Imágen/Documento</label>
							<input type="text" name="campo[folio]" class="form-control" id="campo[folio]" value="<?php echo $campos['folio']?>" <?php echo $disabled?> />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Asiento/Ficha</label>
							<input type="text" name="campo[asiento]" class="form-control" id="campo[asiento]" value="<?php echo $campos['asiento']?>" <?php echo $disabled?> />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2 Dirección" >
							<label>D&iacute;gito verificador</label>
							<input type="input-left-addon" name="campo[digverificador]" class="form-control" id="campo[digverificador]" data-inputmask="'mask':'9{1,9}','greedy':false" value="<?php echo $campos['digverificador']?>" <?php echo $disabled?> />
						</div>
					</div>
					<div class="row">
						
						<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-7  ">
							<label>Dirección</label>
							<input type="text" name="campo[direccion]" class="form-control" id="campo[direccion]" value="<?php echo $campos['direccion']?>" <?php echo $disabled?> />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<?php  
							$activo="";
							$por_abrobar="";
							$inactivo="";
							if($campos['estado']=="Por abrobar") 
								$por_abrobar="selected";
							if($campos['estado']=="Activo") 
								$activo="selected";
							if($campos['estado']=="Inactivo") 
								$inactivo="selected";
							
							?>
							<label>Estado</label>
								<select name="campo[estado]" id="campo[estado]" class="form-control" <?php echo $disabled?> >
							<?php
							if($campos['politicas_general'] >0)
							{
								if((in_array(8, $campos['politicas']) || in_array(9, $campos['politicas']) || in_array(10, $campos['politicas'])) && $campos['uuid_aseguradora']!="")
								{
									if($campos['estado']=="Por aprobar")
									{
										if(in_array(8, $campos['politicas'])) {
											?>
											<option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
											<option value='Activo' <?php echo $activo ?> >Activo</option>
											<?php
										}
										else
										{
											?>
											<option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
											<?php
										}
									}
									else if($campos['estado']=="Activo")
									{
										if(in_array(9, $campos['politicas'])) {
											?>
											<option value='Activo' <?php echo $activo ?> >Activo</option>
											<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
											<?php
										}
										else
										{
											?>
											<option value='Activo' <?php echo $activo ?> >Activo</option>
											<?php
										}
									}
									else if($campos['estado']=="Inactivo")
									{
										if(in_array(10, $campos['politicas'])) {
											?>
											<option value='Activo' <?php echo $activo ?> >Activo</option>
											<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
											<?php
										}
										else
										{
											?>
											<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
											<?php
										}
									}
								}
								else
								{
									if($campos['estado']=="Por aprobar" || $campos['estado']=="")
									{
										?>
										<option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
										<option value='Activo' <?php echo $activo ?> >Activo</option>
										<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
										<?php
									}
									else
									{
										?>
										<option value='Activo' <?php echo $activo ?> >Activo</option>
										<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
										<?php
									}
								}
							}
							else
							{
								if($campos['estado']=="Por aprobar" || $campos['estado']=="")
								{
									?>
									<option value='Por aprobar' <?php echo $por_abrobar ?> >Por Aprobar</option>
									<option value='Activo' <?php echo $activo ?> >Activo</option>
									<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
									<?php
								}
								else
								{
									?>
									<option value='Activo' <?php echo $activo ?> >Activo</option>
									<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
									<?php
								}
							}
							?>
							</select>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
			<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('aseguradoras/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>
			<?php if($campos['guardar']==1){?>
			<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
				<input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
			</div>
			<?php }?>
		</div>
		<?php echo form_close(); ?>
		</div>           
	</div>
</div>

<div id="formulariocontacto">
	<?php
		echo modules::run('aseguradoras/ocultoformulariocontacto',$campos);
	?>
</div>
