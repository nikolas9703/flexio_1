<?php echo form_open_multipart(base_url('polizas/ajax_guardar_documentos'), "id='guardarDocumento'"); ?>
<input type="hidden" name="id_poliza" id="id_poliza" value="" />
<div class="tab-content" style="height: 190px !important;">
	<div class="row docentregados">
		<div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" >
			<label>Documento</label>
			<div class="row">
				<div class='file_upload_poliza' id='fpoliza1'>
					<input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" />
					<input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
				</div>
				<div id='file_tools_poliza' style="width: 90px!important; float: left;">
						<button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_poliza"><i class="fa fa-plus"></i>
						</button>
						<button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_poliza"><i class="fa fa-trash"></i>
						</button>
				</div>
			</div>
		</div>
		<div class="row botones_documentos">
				<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
				<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
					<input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block guardarPoliza" id="campo[guardar]" />
				</div>
			</div>
		
	</div>
</div>
<?php echo form_close(); ?>