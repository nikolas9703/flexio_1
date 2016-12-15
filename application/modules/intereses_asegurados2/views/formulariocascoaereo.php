<div class="row" style="margin-right:0px!important;">
<div class="ibox-title border-bottom">
<h5>Datos del casco aéreo</h5>
<hr style="margin-top:30px!important;">
<div id="vistaPersona">
<div class="tab-content">
<div id="datosdelPersona-5" class="tab-pane active">
<?php echo form_open_multipart(base_url('intereses_asegurados/guardar_aereo'), "id='casco_aereo'"); ?>
<input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_aereo">
<input type="hidden" name="campo[tipo_id]" id="tipo_id_aereo" class="tipo_id" value="3">
<div class="ibox">
<div class="ibox-content m-b-sm" style="display: block; border:0px">
<div class="row">
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>N°. de serie del casco
<span required="" aria-required="true">*</span></label>
<input type="text" name="campo[serie]" id="serie_aereo" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control" data-rule-required="true">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Marca
</label>
<input type="text" name="campo[marca]" id="marca_aereo" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Modelo
</label>
<input type="text" name="campo[modelo]" id="modelo_aereo" value="<?php if (isset($info['ajustadores'])) {
echo $info['ajustadores']['nombre'];
} ?>" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Matrícula
</label>
<input type="text" name="campo[matricula]" id="matricula_aereo" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Valor
</label>
<div class="input-group">
<span class="input-group-addon">$</span>   
<input type="text" name="campo[valor]" id="valor_aereo" value="" class="form-control"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Pasajeros</label>
<input type="text" name="campo[pasajeros]" id="pasajeros_aereo" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tripulación</label>
<input type="text" name="campo[tripulacion]" id="tripulacion_aereo" value="" class="form-control">
</div>
</div>
<div class="row">
<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
<textarea name="campo[observaciones]"
value="" class="form-control" id="observaciones_aereo"></textarea>
<label for="campo[observaciones]" generated="true" class="error"></label>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
<label>Estado </label>
<select class="form-control estado_aereo" name="campo[estado]">
<?php foreach ($estado as  $status) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $status->id_cat ?>"><?php echo $status->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" > </div>
<div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" ><h5>Documentos entregados</h5><hr style="margin-top:30px!important;"></div>
<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6" >
<label>Nombre del documento</label>
<div class='file_upload' id='f1'><input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/></div>
	<div id='file_tools_aereo' style="width: 90px!important; float: left;">
            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_aereo"><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_aereo"><i class="fa fa-trash"></i></button>
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
class="btn btn-primary btn-block guardarAereo" id="campo[guardar]" disabled>
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
