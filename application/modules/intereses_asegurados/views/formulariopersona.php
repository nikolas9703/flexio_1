<div class="row" style="margin-right:0px!important;">
	<div class="ibox-title border-bottom">
		<h5>Datos de la persona</h5>
		<hr style="margin-top:30px!important;">
		<div id="vistaPersona">
			<div class="tab-content">
				<div id="datosdelPersona-5" class="tab-pane active">
					<?php echo form_open_multipart(base_url('intereses_asegurados/guardar'), "id='persona'"); ?>
					<input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid">
					<input type="hidden" name="campo[tipo_id]" id="tipo_id_persona" class="tipo_id" value="5">
					<div class="ibox">
						<div class="ibox-content m-b-sm" style="display: block; border:0px">
							<div class="row">
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 nombreAjustadores"><label>Nombre completo
									<span required="" aria-required="true">*</span></label>
									<input type="text" name="campo[nombre]" id="campo[nombre]" value="" class="form-control" id="campo[nombre]" data-rule-required="true">
								</div>
								<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
									<label>Identificaci&oacute;n <span required="" aria-required="true">*</span></label>
									<select data-rule-required="true" class="form-control identificacion" name="campo[identificacion]">
										<?php foreach ($tipo_identificacion as  $tipo_iden) {  ?>
										<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $tipo_iden->id_cat ?>"><?php echo $tipo_iden->etiqueta ?></option>
										<?php }  ?>
									</select>
								</div>
								<div class="noPAS">
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
										<label>Provincia <span required="" aria-required="true">*</span></label>
										<select data-rule-required="true" class="form-control provincia" name="campo[provincia]">
											<option value="">Seleccione</option>
											<?php 
											$sum=0;
											foreach ($info['provincias'] as  $provincia) {  ?>
											<option <?php if(isset($info['ajustadores']['provincia'])){ if($provincia->key == $info['ajustadores']['provincia'] ) {echo ' selected';} } ?> value="<?php echo $sum+= count($provincia); ?>"><?php echo $provincia->etiqueta ?></option>
											<?php }  ?>
										</select>
									</div>                            
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
										<label>Letras <span required="" aria-required="true">*</span></label>
										<select data-rule-required="true" class="form-control letra" name="campo[letra]" id="campo[letra]">
											<option value="">Seleccione</option>
											<?php foreach ($info['letras'] as  $letra) { ?>
											<option <?php if(isset($info['ajustadores']['letra'])){ if($letra->key == $info['ajustadores']['letra'] ) {echo ' selected';} } ?> value="<?php echo $letra->etiqueta ?>"><?php echo $letra->etiqueta ?></option>
											<?php }  ?>
										</select>
									</div>                            
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2">
										<label>Tomo <span required="" aria-required="true">*</span></label>
										<input data-rule-required="true" value="<?php if(isset($info['ajustadores']['tomo'])){ echo $info['ajustadores']['tomo']; } ?>" type="text" id="campo[tomo]" name="campo[tomo]" class="form-control tomo">
									</div>
									<div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2">
										<label>Asiento <span required="" aria-required="true">*</span></label>
										<input data-rule-required="true" value="<?php if(isset($info['ajustadores']['asiento'])){ echo $info['ajustadores']['asiento']; } ?>" type="text" id="campo[asiento]" name="campo[asiento]" class="form-control asiento">
									</div>
								</div>
								<div class="PAS">
									<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
										<label>No. Pasaporte <span required="" aria-required="true">*</span></label>
										<input data-rule-required="true" value="<?php if(isset($info['ajustadores']['pasaporte'])){ echo $info['ajustadores']['pasaporte']; } ?>" type="text"  id="campo[pasaporte]" name="campo[pasaporte]" class="form-control pasaporte">
									</div>
								</div>

							</div>
							<div class="row">
								<div class="form-group col-xs-6 col-sm-2 col-md-2 col-lg-2 ">
									<label>Fecha de nacimiento</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="input-left-addon" name="campo[fecha_nacimiento]"
										value="<?php if (!empty($data->fecha_nacimiento)) {
											echo $data->fecha_nacimiento;
										} ?>" class="form-control datepicker" id="campo[fecha_nacimiento]">
									</div></div>
									<div class="form-group col-xs-6 col-sm-1 col-md-1 col-lg-1 ">
										<label>Edad</label>
										<input type="input-left-addon" name="campo[edad]"
										value="<?php if (!empty($data->edad)) {
											echo $data->edad;
										} ?>" class="form-control" id="campo[edad]">
									</div>
									<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
										<label>Estado civil <span required="" aria-required="true">*</span></label>
										<select data-rule-required="true" class="form-control estado_civil" name="campo[estado_civil]">
											<option value="">Seleccione</option>
											<?php foreach ($estado_civil as  $row) {?>
											<option <?php if(!empty($data->estado_civil)){ if($row->id_cat == $data->estado_civil ) {echo ' selected';}} ?> value="<?php echo $row->id_cat ?>"><?php echo $row->etiqueta ?></option>
											<?php }  ?>
										</select>
									</div>
									<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 nombreAjustadores"><label>Nacionalidad</label>
										<input type="text" name="campo[nacionalidad]" id="campo[nombre]" value="<?php if (!empty($data->nacionalidad)) {
											echo $data->nacionalidad;
										} ?>" class="form-control" id="campo[nacionalidad]">
									</div>
									<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
										<label>Sexo <span required="" aria-required="true">*</span></label>
										<select data-rule-required="true" class="form-control sexo" name="campo[sexo]">
											<option value="">Seleccione</option>
											<?php foreach ($sexo as  $se) {?>
											<option <?php if(!empty($data->sexo)){ if($se->id_cata == $data->sexo ) {echo ' selected';}} ?> value="<?php echo $se->id_cat ?>"><?php echo $se->etiqueta ?></option>
											<?php }  ?>
										</select>
									</div>
									<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Estatura(mt)</label>
										<input type="text" name="campo[estatura]" id="campo[estatura]" value="<?php if (!empty($data->estatura)) {
											echo $data->estatura;
										} ?>" class="form-control" id="campo[estatura]" data-rule-required="true">
									</div>
									<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 nombreAjustadores"><label>Peso(kg)</label>
										<input type="text" name="campo[peso]" id="campo[peso]" value="<?php if (!empty($data->peso)) {
											echo $data->peso;
										} ?>" class="form-control" id="campo[peso]" data-rule-required="true">
									</div>
									<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
										<div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
											<input type="checkbox" name="campo[telefono_residencial_check]" value="no" class="" id="campo[telefono_residencial_check]">
											<label class="checkbox" for="campo[telefono_residencial_check]"></label>
										</div>
										<label>Teléfono residencial</label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="input-left-addon" name="campo[telefono_residencial]"
											value="" class="form-control telefono_residencial" id="campo[telefono]">
										</div></div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
											<div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
												<input type="checkbox" name="campo[telefono_oficina_check]" value="no" class="" id="campo[telefono_oficina_check]">
												<label class="checkbox" for="campo[telefono_oficina_check]"></label>
											</div>
											<label>Teléfono oficina </label>
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="input-left-addon" name="campo[telefono_oficina]"
												value="" class="form-control telefono_oficina" id="campo[telefono_oficina]">
											</div></div> 
											<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Direcci&oacute;n residencial</label>
												<div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
													<input type="checkbox" name="campo[direccion_residencial_check]" value="no" class="" id="campo[direccion_residencial_check]">
													<label class="checkbox" for="campo[direccion_residencial_check]"></label>
												</div>
												<input type="input-left-addon" name="campo[direccion_residencial]"
												value="<?php if (!empty($data->direccion_residencial)) {
													echo $data->direccion_residencial;
												} ?>" class="form-control" id="campo[direccion]">
												<label for="campo[direccion]" generated="true" class="error"></label>
											</div>
											<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Direcci&oacute;n laboral</label>
												<div class="checkbox m-r-xs" style="padding-left: 20px;position: relative;float: left;margin-top: -10px;height: 5px;">    
													<input type="checkbox" name="campo[direccion_laboral_check]" value="no" class="" id="campo[direccion_laboral_check]">
													<label class="checkbox" for="campo[direccion_laboral_check]"></label>
												</div>
												<input type="input-left-addon" name="campo[direccion_laboral]"
												value="<?php if (!empty($data->direccion_laboral)) {
													echo $data->direccion_laboral;
												} ?>" class="form-control" id="campo[direccion]">
												<label for="campo[direccion_laboral]" generated="true" class="error"></label>
											</div>
											<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
												<textarea name="campo[observaciones]"
												value="<?php if (!empty($data->observaciones)) {
													echo $data->observaciones;
												} ?>" class="form-control" id="observaciones_persona"></textarea>
												<label for="campo[observaciones]" generated="true" class="error"></label>
											</div>
											<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
												<label>Estado <span required="" aria-required="true">*</span></label>
												<select data-rule-required="true" class="form-control estado" name="campo[estado]">
													<?php foreach ($estado as  $status) {?>
													<option <?php if(!empty($data->estado)){ if($status->id_cat == $data->estado ) {echo ' selected';}} ?> value="<?php echo $status->id ?>"><?php echo $status->etiqueta ?></option>
													<?php }  ?>
												</select>
											</div>
											<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div1_persona">
											</div>    
											<div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12 div2_persona"><h5>Documentos entregados</h5><hr style="margin-top:30px!important;"></div>
											<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6 div3_persona" >
												<label>Nombre del documento</label>
												<div class='file_upload_persona' id='f1'><input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/></div>
												<div id='file_tools_persona' style="width: 90px!important; float: left;">
													<button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_persona"><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_persona"><i class="fa fa-trash"></i></button>
												</div>
											</div>    
											<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-6" >
											</div>

										</div>

										<div class="row botones">
											<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
											<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" ><a
												href="<?php echo base_url('intereses_asegurados/listar'); ?>" class="btn btn-default btn-block"
												id="cancelar">Cancelar </a></div>
												<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
													<input type="submit" name="campo[guardar]" value="Guardar "
													class="btn btn-primary btn-block guardarPersona" id="campo[guardar]">
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
				<div class="alert alert-warning" ng-if="notificacion.campos.length || notificacion.limiteCapacidadAlcanzado.length">
					<div ng-if="notificacion.campos.length">
						<b>No es posible crear un nuevo descuento para este colaborador.</b><br> <b>Los siguientes datos deben ser completados en detalle del colaborador:</b>
						<ul>
							<li ng-repeat="notificacion in notificacion.campos track by $index" ng-bind-html="notificacion"></li>
						</ul>
					</div>
					<div ng-if="notificacion.limiteCapacidadAlcanzado.length" ng-bind-html="notificacion.limiteCapacidadAlcanzado">
					</div>
				</div>
