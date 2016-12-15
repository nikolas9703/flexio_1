<div id="wrapper">
    <?php Template::cargar_vista('sidebar');?>
<div id="page-wrapper" class="gray-bg row">

<?php Template::cargar_vista('navbar');?>
<div class="row border-bottom"></div>
<?php Template::cargar_vista('breadcrumb'); //Breadcrumb?>


	<div class="wrapper wrapper-content">

    	<div class="row">
                <div id="mensaje_info">

                </div>

                <div class="panel-group" aria-multiselectable="true" role="tablist">

        	    		<div class="panel panel-blanco" id="accordeonCatalogos">
        					<div class="panel-heading panel-blanco-heading">
        						<h5 class="panel-title">
        							<a class="" data-toggle="collapse" data-parent="#administrador_de_actividades" href="#collapse-administrador_de_actividades" aria-expanded="true">Oportunidad</a>
        						</h5>
        					</div>
        					<div style="" id="collapse-administrador_de_actividades" class="panel-collapse collapse in" aria-expanded="true">
        						<div class="panel-body">


        							<div class="table-responsive1">
            <?php
            $formAttr = array(
              'method'       => 'POST',
              'id'           => 'crearNotificacionForm',
              'autocomplete' => 'off'
            );
            echo form_open("", $formAttr);

            ?>
            <table class="table table-noline tabla-dinamica" id="notificacionesTable">
							      <thead>
							         <tr>
							            <th>Nombre del Reporte <span required="" aria-required="true">*</span></th>
							            <th>Habilitado</th>
							            <th>Aviso <span required="" aria-required="true">*</span></th>
							            <th>Rol <span required="" aria-required="true">*</span></th>
							            <th>Usuario <span required="" aria-required="true">*</span></th>
							            <th colspan="2">&nbsp;</th>
							         </tr>
							      </thead>
							      <tbody>
                      <?php  if(empty($jobs)){ ?>
							         <tr id="0">
							            <td with="30%">
                            <select size="2" id="descripcion0" name="reporte[0][id_reporte]" class="form-control chosen-select" data-rule-required="true" data-msg-required="Debe llenar todos los campos marcados con *.">
                              <option selected="selected" value="">Seleccione</option>
                              <?php
                                   if(!empty($reportes)){
                                     foreach($reportes as $info){ ?>
                                 <option value="<?php echo $info['id']; ?>"><?php echo $info['descripcion']; ?></option>
                              <?php } }?>
                            </select>
                          </td>
							            <td>
                            <div class="checkbox checkbox-success"  align="center">
                              <input id="habilitado0" name="reporte[0][activo]" type="checkbox" class="form-control notificaciones-checkbox" value="" />
                              <label for="habilitado0"></label>
                            </div>
                          </td>
							            <td>
                            <input type="text" id="fecha_ejecucion0" name="reporte[0][fecha_ejecucion]" value="" class="form-control daterange-picker" data-rule-required="true" data-msg-required="Debe llenar todos los campos marcados con *."/>
                          </td>
							            <td>
                            <select id="id_rol0" name="reporte[0][id_rol]" class="chosen-select form-control role-change" data-rule-required="true" data-msg-required="Debe llenar todos los campos marcados con *.">
                              	<option value=""  selected="selected">Seleccione</option>
                                <?php
                                    if(!empty($roles)){
                                     foreach($roles as $info){ ?>
                                 <option value="<?php echo $info['id_rol'] ; ?>"><?php echo $info['nombre_rol'] ; ?></option>
                                <?php } }?>
                           </select>
							            </td>
							            <td width="20%">
                            <select id="id_usuario0" data-placeholder="Seleccione los usuarios" multiple="multiple" size="1"   name="reporte[0][id_usuario][]" class="form-control chosen-select chosen-usuarios hasUsuarios">
                              <option value="" class="hide">Seleccione</option>
                            </select>
                          </td>
							            <td class="hide"><button class="btn btn-default btn-block eliminarBtn hide" type="button"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;Eliminar</span></button></td>
							            <td><button id="agregarBtn" class="btn btn-default btn-block agregarBtn" type="button" name=""><i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;Agregar</span></button></td>
							         </tr>

                      <?php }
                          if(!empty($jobs)){
                            $i= 0;
                            foreach ($jobs as $tarea) {?>
                              <tr id="<?php echo $i;?>">
       							            <td>
                                  <input type="hidden" name="reporte[<?php echo $i;?>][id]" id="id<?php echo $i;?>" value="<?php echo $tarea['id'];?>">
                                   <select  id="descripcion<?php echo $i;?>" name="reporte[<?php echo $i;?>][id_reporte]" class="form-control chosen-select" data-rule-required="true">

                                     <?php foreach($reportes as $info){ ?>
                                        <option <?php echo ($info['id']==$tarea['id_job']?'selected="selected"':"")?> value="<?php echo $info['id']; ?>"><?php echo $info['descripcion']; ?></option>
                                     <?php }?>
                                   </select>
                                 </td>
       							            <td>
                                   <div class="checkbox checkbox-success"  align="center">
                                     <input id="habilitado<?php echo $i;?>" name="reporte[<?php echo $i;?>][activo]" type="checkbox" class="form-control notificaciones-checkbox" value="<?php echo $tarea['estado'] ?>" <?php echo ($tarea['estado']=='activo'?'checked="checked"':"")?> />
                                     <label for="habilitado<?php echo $i;?>"></label>
                                   </div>
                                 </td>

                              <td><input type="text" id="fecha_ejecucion<?php echo $i;?>" name="reporte[<?php echo $i;?>][fecha_ejecucion]" value="<?php echo date('Y-m-d H:i:s a',strtotime($tarea['tiempo_ejecucion'])); ?>" class="form-control daterange-picker" data-rule-required="true" /></td>

       							            <td>
                                   <select id="id_rol<?php echo $i;?>" name="reporte[<?php echo $i;?>][id_rol]" class="chosen-select form-control role-change" data-rule-required="true">

                                       <?php foreach($roles as $info){ ?>
                                        <option <?php echo ($info['id_rol']==$tarea['id_rol']?'selected="selected"':"")?> value="<?php echo $info['id_rol'] ; ?>"><?php echo $info['nombre_rol'] ; ?></option>
                                       <?php }?>
                                  </select>
       							            </td>
       							            <td>
                                   <select id="id_usuario<?php echo $i;?>" data-placeholder="Seleccione los usuarios" multiple="multiple" size="1"   name="reporte[<?php echo $i;?>][id_usuario][]" class="form-control chosen-select chosen-usuarios hasUsuarios">
                                     <?php $usuarios = self::$ci->roles_model->seleccionar_usuarios_por_rol($tarea['id_rol']);
                                           $json = json_decode($tarea['uuid_usuarios']);

                                           foreach ($usuarios as $usuario) {
                                           ?>
                                          <option <?php echo ($json->mostrar && in_array($usuario['uuid_usuario'],$json->uuid_usuarios)?'selected="selected"':"")?> value="<?php echo $usuario['uuid_usuario'] ?>"><?php echo $usuario['nombre'] ?></option>
                                     <?php } ?>
                                   </select>
                                 </td>
       							            <td><button data-row="<?php echo $i; ?>" data-delete="<?php echo $tarea['id'];?>" class="btn btn-default btn-block eliminarJobBtn" type="button"><i class="fa fa-trash"></i><span>&nbsp;Eliminar</span></button></td>
       							            <td class="hide"><button  class="btn btn-default btn-block" type="button" name=""><i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md">&nbsp;Agregar</span></button></td>
       							         </tr>

                <?php $i++; }
                          } ?>

							      </tbody>
                    <tfoot>
										<tr>
								   			<td class="formerror"></td>
								   		</tr>
								  	</tfoot>
							   </table>
                 <div class="row">
                   <div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
                     <input type="button" id="cancelarFormBtn" class="btn btn-w-m btn-default" value="Cancelar" />
                     <input type="button" id="guardarReporteBtn" class="btn btn-w-m btn-primary" value="Guardar" />
                   </div>
                 </div>
                 <input type="hidden" name="tipo" id="tipo" value="oportunidad">
          <?php echo form_close(); ?>

 						</div>
					</div>
			</div>



		</div>

</div>
</div>
</div>
</div>
</div>
