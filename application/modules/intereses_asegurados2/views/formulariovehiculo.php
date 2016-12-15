<div class="row" style="margin-right:0px!important;">
<div class="ibox-title border-bottom">
<h5>Datos del vehículo</h5>
<hr style="margin-top:30px!important;">
<div id="vistaPersona">
<div class="tab-content">
<div id="datosdelPersona-5" class="tab-pane active">
<?php echo form_open_multipart(base_url('intereses_asegurados/guardar_vehiculo'), "id='vehiculo'"); ?>
<input type="hidden" name="campo[uuid]" id="uuid_vehiculo" class="form-control">
<input type="hidden" name="campo[tipo_id]" id="tipo_id_vehiculo" class="tipo_id" value="8">
<div class="ibox">
<div class="ibox-content m-b-sm" style="display: block; border:0px">
<div class="row">
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>N°. Chasis o serie
<span required="" aria-required="true">*</span></label>
<input type="text" name="campo[chasis]" id="chasis" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control" data-rule-required="true">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>N°. Unidad
</label>
<input type="text" name="campo[unidad]" id="unidad" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Marca
</label>
<input type="text" name="campo[marca]" id="marca" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control marca_vehiculo">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Modelo
</label>
<input type="text" name="campo[modelo]" id="modelo" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control modelo_vehiculo">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Placa
</label>
<input type="text" name="campo[placa]" id="placa" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Año
</label>
<input type="text" name="campo[ano]" id="ano" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Motor
</label>
<input type="text" name="campo[motor]" id="motor" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Color
</label>
<input type="text" name="campo[color]" id="color" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Capacidad de personas
</label>
<input type="text" name="campo[capacidad]" id="capacidad" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>    
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Uso
</label>
<select class="form-control uso_vehiculo" name="campo[uso]">
<option value="">Seleccione</option>
<?php foreach ($uso as  $u) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $u->id_cat ?>"><?php echo $u->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Condición
</label>
<select class="form-control condicion_vehiculo" name="campo[condicion]">
<option value="">Seleccione</option>
<?php foreach ($condicion as  $con) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $con->id_cat ?>"><?php echo $con->etiqueta ?></option>
<?php }  ?>
</select>
</div>   
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Operador
</label>
<input type="text" name="campo[operador]" id="operador" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Extras
</label>
<input type="text" name="campo[extras]" id="extras" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Valor de los extras
</label>
<div class="input-group">
<span class="input-group-addon">$</span>   
<input type="text" name="campo[valor_extras]" id="valor_extras" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Acreedor</label>
<select class="form-control acreedor" name="campo[acreedor]">
<option value="">Seleccione</option>
<?php foreach ($acreedores as  $acre) { ?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $acre->id ?>"><?php echo $acre->nombre ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>% Asignado al acreedor
</label>
<div class="input-group">
<span class="input-group-addon">%</span>
<input type="text" name="campo[porcentaje_acreedor]" id="porcentaje_acreedor" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control porcentaje_vehiculo"></div>
</div>    
</div>
<div class="row">
<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
<textarea name="campo[observaciones]"
value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['direccion_laboral'];
} ?>" class="form-control" id="observaciones_vehiculo"></textarea>
<label for="campo[observaciones]" generated="true" class="error"></label>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
<label>Estado </label>
<select class="form-control estado" name="campo[estado]">
<?php foreach ($estado as  $status) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $status->id_cat ?>"><?php echo $status->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" > </div>
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div1_persona">
</div> 
<div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" ><h5>Documentos entregados</h5></div>
<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6" >
<label>Nombre del documento</label>
<div class='file_upload' id='f1'><input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/></div>
	<div id='file_tools_vehiculo' style="width: 90px!important; float: left;">
            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_vehiculo"><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_vehiculo"><i class="fa fa-trash"></i></button>
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
class="btn btn-primary btn-block guardarVehiculo" id="campo[guardar]" disabled>
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
