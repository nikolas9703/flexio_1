<div class="row" style="margin-right:0px!important;">
<div class="ibox-title border-bottom">
<h5>Datos del Proyecto / Actividad</h5>
<hr style="margin-top:30px!important;">
<div id="vistaPersona">
<div class="tab-content">
<div id="datosdelPersona-5" class="tab-pane active">
<?php echo form_open_multipart(base_url('intereses_asegurados/guardar_proyecto'), "id='proyecto_actividad'"); ?>
<input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_proyecto">
<input type="hidden" name="campo[tipo_id]" id="tipo_id_proyecto" class="tipo_id" value="6">
<div class="ibox">
<div class="ibox-content m-b-sm" style="display: block; border:0px">
<div class="row">
<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"><label>Nombre del proyecto o actividad
<span required="" aria-required="true">*</span></label>
<input type="text" name="campo[nombre_proyecto]" id="nombre_proyecto" value="" class="form-control" data-rule-required="true">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Contratista
</label>
<input type="text" name="campo[contratista]" id="contratista_proyecto" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Representante legal
</label>
<input type="text" name="campo[representante_legal]" id="representante_legal_proyecto" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de concurso
</label>
<div class="input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
<input type="text" name="campo[fecha_concurso]" id="fecha_concurso" value="" class="form-control fecha_concurso"></div>
</div>    
<div class="form-group col-xs-12 col-sm-3 col-md-34 col-lg-3"><label>No. de orden o contrato
</label>
<input type="text" name="campo[no_orden]" id="no_orden_proyecto" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Duración del contrato
</label>
<input type="text" name="campo[duracion]" id="duracion_proyecto" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de inicio
</label>
<div class="input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
<input type="text" name="campo[fecha]" id="fecha" value="" class="form-control fecha_proyecto"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tipo de fianza</label>
<select class="form-control tipo_fianza" name="campo[tipo_fianza]">
<option value="">Seleccione</option>
<?php foreach ($tipo_fianza as  $propuesta) { ?>
<option value="<?php echo $propuesta->valor ?>"><?php echo $propuesta->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Monto del contrato
</label>
<div class="input-group">
<span class="input-group-addon">$</span>   
<input type="text" name="campo[monto]" id="monto" value="" class="form-control monto_proyecto"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Monto afianzado %
</label>
<div class="input-group">  
<input type="text" name="campo[monto_afianzado]" id="monto_afianzado" value="" class="form-control"><span class="input-group-addon">%</span></div>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Tipo de propuesta</label>
<select class="form-control tipo_propuesta" name="campo[tipo_propuesta]" disabled>
<option value="">Seleccione</option>
<?php foreach ($tipo_propuesta as  $propuesta) { ?>
<option value="<?php echo $propuesta->id_cat ?>"><?php echo $propuesta->etiqueta ?></option>
<?php }  ?>
<option value="otro">Otro</option>
</select>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label style="color: transparent">a</label>
<input type="text" class="form-control" value="" name="campo[tipo_propuesta_opcional]" id="tipo_propuesta" disabled/>
</div>
<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"><label>Ubicación
</label>
<input type="text" name="campo[ubicacion]" id="ubicacion_proyecto" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Acreedor</label>
<select class="form-control acreedor_proyecto" name="campo[acreedor]">
<option value="">Seleccione</option>
<?php foreach ($acreedores as  $acre) { ?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $acre->id ?>"><?php echo $acre->nombre ?></option>
<?php }  ?>
<option value="otro">Otro</option>
</select>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label style="color: transparent">a</label>
<input type="text" class="form-control" value="" name="campo[acreedor_opcional]" id="acreedor_opcional" disabled/>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Validez de la fianza</label>
<select class="form-control validez_fianza" name="campo[validez_fianza]">
<option value="">Seleccione</option>
<?php foreach ($validez_fianza as  $fianza) { ?>
<option value="<?php echo $fianza->id_cat ?>"><?php echo $fianza->etiqueta ?></option>
<?php }  ?>
<option value="otro">Otro</option>
</select>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label style="color: transparent">a</label>
<input type="text" class="form-control" value="" name="campo[validez_fianza_opcional]" id="validez_fianza_opcional" disabled/>
</div>    
</div>
<div class="row">
<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
<textarea name="campo[observaciones]"
value="" class="form-control" id="observaciones_proyecto"></textarea>
<label for="campo[observaciones]" generated="true" class="error"></label>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
<label>Estado </label>
<select class="form-control estado_proyecto" name="campo[estado]">
<?php foreach ($estado as  $status) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $status->id_cat ?>"><?php echo $status->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" > </div>
<div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" ><h5>Documentos entregados</h5><hr style="margin-top:30px!important;"></div>
<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6" >
<label>Nombre del documento</label>
<div class='file_upload_proyectos' id='f1'><input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/></div>
	<div id='file_tools_proyectos' style="width: 90px!important; float: left;">
            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_proyectos"><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_proyectos"><i class="fa fa-trash"></i></button>
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
class="btn btn-primary btn-block guardarProyecto" id="campo[guardar]" disabled>
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
