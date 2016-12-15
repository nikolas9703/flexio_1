<div id="wrapper">
<?php

function findKey($keySearch, $array) {
	foreach ($array AS $key => $item){
		if(!empty($item[$keySearch])){
			return $key;
		}
	}
	return false;
}

Template::cargar_vista('sidebar');
?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

	    <div class="col-lg-12">
	        <div class="wrapper-content">
	            <div class="row">

	             <div class="alert alert-success alert-dismissable message-box <?php echo !empty($message) ? 'show' : 'hide'  ?>">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <?php echo !empty($message) ? $message : ''  ?>
                </div>

                <!-- White Background -->

					<?php
					$formAttr = array(
						'method'        => 'post',
						'id'            => 'roleForm',
						'autocomplete'  => 'off'
					);
					echo form_open(base_url(uri_string()), $formAttr);
  					?>


					<!--<p>Asigne los permisos al modulo o modulos que le quiera dar acceso a este rol.</p>-->

					<!-- BEGIN ACORDEON -->
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
						<?php
							if(!empty($grupo_modulos)):
								$mod_counter = 0;
								foreach ($grupo_modulos AS $nombre_grupo => $modulos):
									$modulo_id = (!empty($modulo['modulo_id']) ? $modulo['modulo_id'] : "");
									$suffix_nombre_grupo = strtolower(str_replace(" ", "_", $nombre_grupo)) . date('is');
						?>
						<div id="<?php echo $suffix_nombre_grupo; ?>" class="panel panel-blanco">
							<div class="panel-heading panel-blanco-heading">
								<h5 class="panel-title">
									<a href="#collapse-<?php echo $suffix_nombre_grupo; ?>" data-parent="#<?php echo $suffix_nombre_grupo; ?>" data-toggle="collapse" class="collapsed"><?php echo str_replace(" De ", " de ", ucwords($nombre_grupo)); ?></a>
								</h5>
							</div>
							<div class="panel-collapse collapse" id="collapse-<?php echo $suffix_nombre_grupo; ?>" style="height: 0px;">
								<div class="panel-body">


								<div class="panel-group" id="accordion-addons" role="tablist" aria-multiselectable="true">
								  	<?php
									foreach ($modulos AS $controller_name => $modulo):
										$modulo_id = (!empty($modulo['modulo_id']) ? $modulo['modulo_id'] : "");

										$selectedModuleKey  = findKey($controller_name, (!empty($rol_info['modulos']) ? $rol_info['modulos'] : array()));
										$selectedModule = !empty($rol_info['modulos']) && !empty($rol_info['modulos'][$selectedModuleKey]) && !empty($rol_info['modulos'][$selectedModuleKey][$controller_name]) ? $rol_info['modulos'][$selectedModuleKey][$controller_name] : array();
									?>
									<div class="panel panel-default">
									    <div class="panel-heading" role="tab" id="heading-<?php echo $controller_name; ?>">
											<h4 class="panel-title">
										        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-addons" href="#collapse-<?php echo $controller_name; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $controller_name; ?>">
										        M&oacute;dulo: <?php echo str_replace(" De ", " de ", ucwords($modulo['modulo_nombre'])); ?>
										        </a>
										    </h4>
									    </div>
									    <div id="collapse-<?php echo $controller_name; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo $controller_name; ?>">
										    <div class="panel-body">

										      	<?php echo (!empty($modulo['modulo_descripcion']) ? '<p>'.$modulo['modulo_descripcion'].'</p>' : ""); ?>

												<?php if(!empty($modulo['resources'])): ?>

												<div class="table-responsive">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th rowspan="2" width="8%">Secci&oacute;n</th>
															<th align="center" colspan="<?php echo count($modulo['permissions']) ?>" width="30%">Permisos</th>
														</tr>
														<tr>
														<?php
														if(!empty($modulo['permissions']))
														{
															foreach ($modulo['permissions'] AS $index => $permission_alias)
															{
																echo '<th align="center">'. $permission_alias .'</th>';
															}
														}
														?>
														</tr>
													</thead>
													<tbody>
														<?php

														$cntr = 0;
														$total = count($modulo['resources']);
														$permissions = $modulo['permissions'];
														foreach ($modulo['resources'] AS $resource)
														{
															$resource_name  = $resource['resource_name'];

															//No registrar url's ajax
															if(preg_match("/ajax/i", $resource_name) || preg_match("/oculto/", $resource_name) || preg_match("/filtar/i", $resource_name) || preg_match("/subpanel/i", $resource_name)){
																continue;
															}

															$resource_id    = $resource['id'];
															$resource_name  = $resource_url = str_replace("/(:num)", "", $resource_name);
															$resource_name  = $resource_url = str_replace("/(:any)", "", $resource_name);
															$resource_name  = $resource_url = str_replace("(:an", "", $resource_name);
															$resource_name  = str_replace($controller_name, "", $resource_name);
															$resource_name  = $resourceName = str_replace("/", "", $resource_name);
															$resource_name  = ucwords(str_replace("-", " ", $resource_name));

															$selectedResource = (!empty($selectedModule['resources']) && !empty($selectedModule['resources'][$resource['resource_name']]) ? $selectedModule['resources'][$resource['resource_name']] : "");

															$row = '<tr>
																<td>'. $resource_name .'</td>';

 																if(!empty($permissions)){
																	foreach ($permissions AS $index => $permission_alias)
																	{
																			$fieldId = uniqid(rand(), true); //Generate a random, unique, alphanumeric string.
                                      $selectedPermission = (!empty($selectedResource) && in_array($index, $selectedResource) ? 'checked="checked"' : "");
                                      $selectedPermissionClass = (!empty($selectedResource) && in_array($index, $selectedResource) ? 'remove_perm' : "");
                                      $fieldName = "modulo[$modulo_id][recurso][$resource_id][$index]";
																		//echo $selectedPermission."</BR>";
																		//echo $selectedPermissionClass."</BR>"."</BR>"."</BR>";
																		if (preg_match("/__/i", $index))
                                    {
                                       	if ($resourceName != "" && $index != "")
																				{
																						//Verficar si el nombre del route concuerda con el nombre de permiso
																						if (preg_match("/".$resourceName."_/is", $index))
																						{
																							$row .= '<td align="center"><div class="checkbox checkbox-success"><input type="checkbox" name="'. $fieldName .'" id="'. $fieldId .'" class="'. $selectedPermissionClass .'" '. $selectedPermission .' /><label for="'. $fieldId .'">&nbsp;</label></div></td>';
																						}
																						else
																						{
																							$row .= '<td align="center" style="background-color: #fcfcfc;">&nbsp;</td>';
																						}
	                                      }else{
	                                          $row .= '<td align="center" style="background-color: #fcfcfc;">&nbsp;</td>';
	                                      	}
	                                  }
                                    else
                                    {
                                       $row .= '<td align="center"><div class="checkbox checkbox-success"><input type="checkbox" name="'. $fieldName .'" id="'. $fieldId .'" class="'. $selectedPermissionClass .'" '. $selectedPermission .' /><label for="'. $fieldId .'">&nbsp;</label></div></td>';
                                    }
																	}
																}

															$row .= '</tr>';
															echo $row;

															$cntr++;
														}
														?>
													</tbody>
												</table>
												</div>


												<?php endif; ?>

										    </div>
									    </div>
									</div>
									<?php endforeach; ?>
								</div>

								</div>
							</div>
						</div>
						<?php
							$mod_counter++;
							endforeach;
						endif;
						?>
					</div>

					<!-- END ACORDEON -->
					<!-- <div class="row">
                    	<div class="col-xs-0 col-sm-6 col-md-8">&nbsp;</div>
                    	<div class="form-group col-xs-12 col-sm-3 col-md-2">
                    		<a href="<?php echo base_url("roles/listar-roles") ?>" class="btn btn-default btn-block">Cancelar</a>
                    	</div>
                    	<div class="form-group col-xs-12 col-sm-3 col-md-2">
                        	<button type="submit" class="btn btn-primary btn-block">&nbsp;Guardar</button>
                    	</div>
                    </div> -->


                     <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-6">&nbsp;</div>

                             <div class="form-group col-xs-12 col-sm-6 col-md-6"  style="text-align: right;">
                             	 <a href="<?php echo base_url("roles/listar") ?>" class="btn btn-w-m btn-default">Cancelar</a>
                                <button type="submit" class="btn btn-w-m btn-primary">&nbsp;Guardar</button>
                            </div>
                        </div>




					<input type="hidden" name="role_id" value="<?php echo !empty($rol_id) ? $rol_id : "";  ?>" />
					<?php echo form_close(); ?>

					<?php
					$formAttr = array(
						'method' => 'POST',
						'id'     => 'deletePermisoForm',
						'class'  => 'hide'
					);
					echo form_open(base_url(uri_string()), $formAttr);
					?>
                    <input type="text" id="permiso" name="" />
                    <input type="hidden" name="id_rol" value="<?php echo !empty($rol_id) ? $rol_id : "";  ?>" />
                    <?php echo form_close(); ?>

                <!-- White Background -->
                </div>
            </div>
        </div>

    </div>
</div>
