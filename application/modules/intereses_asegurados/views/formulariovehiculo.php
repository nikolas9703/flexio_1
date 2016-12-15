<div class="row" style="margin-right:0px!important;">
	<div class="ibox-title border-bottom">
		<h5>Datos del vehículo</h5>
		<hr style="margin-top:30px!important;">
		<div id="vistaPersona">
			<div class="tab-content">
				<div id="datosdelPersona-5" class="tab-pane active">
					<?php 
					echo form_open_multipart(base_url('intereses_asegurados/guardar_vehiculo'), "id='vehiculo'"); ?>
					<input type="hidden" name="campo[uuid]" id="uuid_vehiculo" class="form-control" value="<?php if(isset($campos['uuid']))echo $campos['uuid']?>" />
					<input type="hidden" name="campo[tipo_id]" id="tipo_id_vehiculo" class="tipo_id" value="8" />
					<input type="hidden" name="campo[id]" id="id" class="tipo_id" value="<?php if(isset($campos['id']))echo $campos['id']?>" />
					<div class="ibox">
						<div class="ibox-content m-b-sm" style="display: block; border:0px">
							<div class="row">
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>N°. Chasis o serie <span required="" aria-required="true">*</span></label>
									<input type="text" name="campo[chasis]" id="chasis" class="form-control" data-rule-required="true" value="<?php if(isset($campos['datos']['chasis']))echo $campos['datos']['chasis']?>" />
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>N°. Unidad </label>
									<input type="text" name="campo[unidad]" id="unidad" class="form-control" value="<?php if(isset($campos['datos']['unidad']))echo $campos['datos']['unidad']?>"/>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
								<label>Marca</label>
									<input type="text" name="campo[marca]" id="marca" class="form-control marca_vehiculo" value="<?php if(isset($campos['datos']['marca']))echo $campos['datos']['marca']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Modelo</label>
									<input type="text" name="campo[modelo]" id="modelo" class="form-control modelo_vehiculo" value="<?php if(isset($campos['datos']['modelo']))echo $campos['datos']['modelo']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Placa</label>
									<input type="text" name="campo[placa]" id="placa" class="form-control" value="<?php if(isset($campos['datos']['placa']))echo $campos['datos']['placa']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Año</label>
									<input type="text" name="campo[ano]" id="ano" class="form-control" value="<?php if(isset($campos['datos']['ano']))echo $campos['datos']['ano']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Motor</label>
									<input type="text" name="campo[motor]" id="motor" class="form-control" value="<?php if(isset($campos['datos']['motor']))echo $campos['datos']['motor']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Color</label>
									<input type="text" name="campo[color]" id="color" class="form-control" value="<?php if(isset($campos['datos']['color']))echo $campos['datos']['color']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Capacidad de personas</label>
									<input type="text" name="campo[capacidad]" id="capacidad" class="form-control" value="<?php if(isset($campos['datos']['capacidad']))echo $campos['datos']['capacidad']?>">
								</div>    
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Uso
								</label>
									<select class="form-control uso_vehiculo" name="campo[uso]">
									<option value="">Seleccione</option>
									<?php foreach ($uso as  $u) {?>
									<option value="<?php echo $u->id?>" <?php if(isset($campos['datos']['uso']))if($campos['datos']['uso']==$u->id) echo "selected"?>><?php echo $u->etiqueta ?></option>
									<?php }  ?>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Condición</label>
									<select class="form-control condicion_vehiculo" name="campo[condicion]">
									<option value="">Seleccione</option>
									<?php foreach ($condicion as  $con) {?>
									<option value="<?php echo $con->id?>" <?php if(isset($campos['datos']['condicion']))if($campos['datos']['condicion']==$con->id) echo "selected"?>><?php echo $con->etiqueta ?></option>
									<?php }  ?>
									</select>
								</div>   
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Operador</label>
									<input type="text" name="campo[operador]" id="operador" value="<?php if(isset($campos['datos']['operador']))echo $campos['datos']['operador']?>" class="form-control">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Extras</label>
									<input type="text" name="campo[extras]" id="extras" class="form-control" value="<?php if(isset($campos['datos']['extras']))echo $campos['datos']['extras']?>">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Valor de los extras</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>   
									<input type="text" name="campo[valor_extras]" id="valor_extras" class="form-control" value="<?php if(isset($campos['datos']['valor_extras']))echo $campos['datos']['valor_extras']?>"
									></div>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>Acreedor</label>
									<select class="form-control acreedor" name="campo[acreedor]">
									<option value="">Seleccione</option>
									<?php foreach ($acreedores as  $acre) { ?>
									<option value="<?php echo $acre->id?>" <?php if(isset($campos['datos']['acreedor']))if($campos['datos']['acreedor']==$acre->id) echo "selected"?>><?php echo $acre->nombre ?></option>
									<?php }  ?>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
									<label>% Asignado al acreedor</label>
									<div class="input-group">
									<span class="input-group-addon">%</span>
									<input type="text" name="campo[porcentaje_acreedor]" id="porcentaje_acreedor" class="form-control porcentaje_vehiculo" value="<?php if(isset($campos['datos']['porcentaje_acreedor']))echo $campos['datos']['porcentaje_acreedor']?>"></div>
								</div>    
							</div>
							<div class="row">
								<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 ">
									<label>Observaciones</label>
									<textarea name="campo[observaciones]" class="form-control" id="observaciones_vehiculo"><?php if(isset($campos['datos']['observaciones']))echo $campos['datos']['observaciones']?></textarea>
									<label for="campo[observaciones]" generated="true" class="error"></label>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
									<label>Estado </label>
									<select class="form-control estado" name="campo[estado]" id="campo[estado]">
										<?php foreach ($estado as  $status) {?>
										<option value="<?php echo $status->etiqueta?>" <?php if(isset($campos['estado']))if($campos['estado']==$status->etiqueta) echo "selected"?>><?php echo $status->etiqueta?></option>
										<?php }  ?>
									</select>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" > </div>
								<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div1_persona">	
							</div> 
							<?php
							if(!isset($campos['datos']['uuid_vehiculo']))
							{
							?>
							<div class="row">
								<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6" >
									<h5>Documentos entregados</h5><br><br>
									<label>Nombre del documento</label>
									<div class='file_upload' id='f1'>
										<input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
									</div>
									<div id='file_tools_vehiculo' style="width: 90px!important; float: left;">
											<button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_vehiculo"><i class="fa fa-plus"></i>
											</button>
											<button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_vehiculo"><i class="fa fa-trash"></i>
											</button>
									</div>
								</div>
							</div>
							<?php
							}
							?>

							<div class="row botones">
								<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
									<a href="<?php echo base_url('intereses_asegurados/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
									<input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block guardarVehiculo" id="campo[guardar]" />
								</div>
							</div>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div> 
</div>
