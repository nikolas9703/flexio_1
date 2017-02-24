<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formAseguradoraCrearContacto',
    'autocomplete' => 'off'
);
?>
<div id="vistaCliente" class="">
	<div class="tab-content">
		<?php  
			echo form_open(base_url('aseguradoras/agregarcontacto'), $formAttr);
			
			if(isset($campos['opt']))
				$opt=$campos['opt'];
			else
				$opt="";
		?>
		<div id="datosdelaaseguradora-5" class="col-lg-12">
			<input type="hidden" name="campo[uuid_aseguradora]" id="campo[uuid_aseguradora]" value="<?php echo $campos['uuid_aseguradora']?>" />
			<input type="hidden" name="campo[uuid]" id="campo[uuid]" />
			<input type="hidden" name="campo[opt]" id="campo[opt]" value="<?php echo $opt?>" />
			<div class="ibox">  
				<div class="ibox-title">
                        <h5>Datos del Contacto </h5>
						<div style="width: 8%;float: right;padding: 0px;" id="impresioncontacto">
							<input style="height: 31px;" type="button" name="campo[imprimirContacto]" value="Imprimir " class="btn btn-primary btn-block" id="campo[imprimirContacto]" :disabled="disabledSubmit">
						</div>
                </div>
				
				<div class="ibox-content " style="display: block;" id="datosGenerales" >
					<div class="row">
						<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3  ">
							<label>Nombre<span required="" aria-required="true">*</span></label>
							<input type="text" name="campo[nombre]" class="form-control nombre" id="campo[nombre]" data-rule-required="true" />
							
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Correo electrónico <span required="" aria-required="true">*</span></label>
							<div class="input-group">
								<span class="input-group-addon">@</span>
								<input type="input-left-addon" name="campo[email]" data-addon-text="@" class="form-control email"  id="campo[email]" data-rule-required="true" />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Celular </label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-phone"></i></span>
								<input type="input-left-addon" name="campo[celular]" class="form-control celular" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[celular]" />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Teléfono </label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-phone"></i></span>
								<input type="input-left-addon" name="campo[telefono]" class="form-control telefono" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3  ">
							<label>Cargo</label>
							<input type="text" name="campo[cargo]" class="form-control" id="campo[cargo]" />
						</div>
						<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3  ">
							<label>Dirección</label>
							<input type="text" name="campo[direccion]" class="form-control" id="campo[direccion]" />
						</div>
						<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3  ">
							<label>Comentarios</label>
							<input type="text" name="campo[comentarios]" class="form-control" id="campo[comentarios]" />
						</div>
						<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3  ">
							<label>Estado</label>
							<select name="campo[estado]" id="campo[estado]" class="form-control">
								<option value="Activo">Activo</option>
								<option value="Inactivo">Inactivo</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>           
		<?php 
		$url="";
		if($opt!=1)
		{
			$url=base_url('aseguradoras/editar/'.$campos['uuid_aseguradora']);
		}
		else
		{
			$url=base_url('aseguradoras/listar');
		}
		?>
		<div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
			<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo $url; ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>
			<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
				<input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>