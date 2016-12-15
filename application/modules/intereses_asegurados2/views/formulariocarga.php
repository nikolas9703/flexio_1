<div class="row" style="margin-right:0px!important;">
<div class="ibox-title border-bottom">
<h5>Datos de la carga</h5>
<hr style="margin-top:30px!important;">
<div id="vistaPersona">
<div class="tab-content">
<div id="datosdelPersona-5" class="tab-pane active">
<?php echo form_open_multipart(base_url('intereses_asegurados/guardar_carga'), "id='carga'"); ?>
<input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_carga">
<input type="hidden" name="campo[tipo_id]" id="tipo_id_carga" class="tipo_id" value="2">
<div class="ibox">
<div class="ibox-content m-b-sm" style="display: block; border:0px">
<div class="row">
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>No. de liquidación
<span required="" aria-required="true">*</span></label>
<input type="text" name="campo[no_liquidacion]" id="no_liquidacion" value="" class="form-control" data-rule-required="true">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de despacho
</label>
<div class="input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
<input type="text" name="campo[fecha_despacho]" id="fecha_despacho" value="" class="form-control"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de arribo
</label>
<div class="input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
<input type="text" name="campo[fecha_arribo]" id="fecha_arribo" value="" class="form-control"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Detalle de mercancía</label>
<input type="text" name="campo[detalle]" id="detalle_mercancia" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Valor de la mercancía
</label>
<div class="input-group">
<span class="input-group-addon">$</span>   
<input type="text" name="campo[valor]" id="valor_mercancia" value="" class="form-control"></div>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tipo de empaque
</label> 
<select class="form-control tipo_empaque" name="campo[tipo_empaque]">
<option value="">Seleccione</option>
<?php foreach ($tipo_empaque as  $tipo_emp) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $tipo_emp->id_cat ?>"><?php echo $tipo_emp->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Condición de envío</label>
<select class="form-control condicion_carga" name="campo[condicion_envio]">
<option value="">Seleccione</option>
<?php foreach ($condicion_envio as  $cond) { ?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $cond->id_cat ?>"><?php echo $cond->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Medio de Transporte</label>
<select class="form-control medio_transporte" name="campo[medio_transporte]">
<option value="">Seleccione</option>
<?php foreach ($medio_transporte as  $medio) { ?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $medio->id_cat ?>"><?php echo $medio->etiqueta ?></option>
<?php }  ?>
</select>
</div>  
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Origen
</label>
<input type="text" name="campo[origen]" id="origen_carga" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Destino
</label>
<input type="text" name="campo[destino]" id="destino_carga" value="" class="form-control">
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Acreedor</label>
<select class="form-control acreedor_carga" name="campo[acreedor]">
<option value="">Seleccione</option>
<?php foreach ($acreedores as  $acre) { ?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $acre->id ?>"><?php echo $acre->nombre ?></option>
<?php }  ?>
<option value="otro">Otro</option>
</select>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label></label>
<input type="text" class="form-control" value="" name="campo[acreedor_opcional]" id="acreedor_carga_opcional" disabled/>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Tipo de obigaci&oacute;n</label>
<select class="form-control tipo_obligacion" name="campo[tipo_obligacion]">
<option value="">Seleccione</option>
<?php foreach ($tipo_obligacion as  $obligacion) { ?>
<option value="<?php echo $obligacion->id_cat ?>"><?php echo $obligacion->etiqueta ?></option>
<?php }  ?>
<option value="otro">Otro</option>
</select>
</div>
<div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label></label>
<input type="text" class="form-control" value="" name="campo[tipo_obligacion_opcional]" id="tipo_obligacion_opcional" disabled/>
</div>    
</div>
<div class="row">
<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
<textarea name="campo[observaciones]"
value="" class="form-control" id="observaciones_carga"></textarea>
<label for="campo[observaciones]" generated="true" class="error"></label>
</div>
<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
<label>Estado </label>
<select class="form-control estado_carga" name="campo[estado]">
<?php foreach ($estado as  $status) {?>
<option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $status->id_cat ?>"><?php echo $status->etiqueta ?></option>
<?php }  ?>
</select>
</div>
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" > </div>
<div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" ><h5>Documentos entregados</h5><hr style="margin-top:30px!important;"></div>
<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6" >
<label>Nombre del documento</label>
<div class='file_upload_carga' id='f1'><input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/></div>
	<div id='file_tools_carga' style="width: 90px!important; float: left;">
            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_carga"><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_carga"><i class="fa fa-trash"></i></button>
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
class="btn btn-primary btn-block guardarCarga" id="campo[guardar]" disabled>
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
