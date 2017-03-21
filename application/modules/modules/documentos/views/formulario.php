<?php 
$campos = array();
$documentoClass = strtolower(get_class($this));
$currentClass = $this->router->fetch_class();

$formAttr = array(
	'method'        => 'POST',
	'id'            => 'subirDocumentosForm',
	'autocomplete'  => 'off',
	'ng-controller' => 'subirDocumentosController'
);
echo form_open(base_url("$currentClass/ajax_guardar_documentos"), $formAttr);

//Verificar que la clase actual sea distinta a la clase de documentos
if($documentoClass != $currentClass){
	$camposClass = new Campos($currentClass);
	$campos = $camposClass->get();
}
 
if(!empty($campos))
{
	$html = '<div class="row m-b">';
	foreach($campos AS $label => $campo)
	{
		$html .= '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		<label for="">'. $label .'</label>
		'. $campo .'
		</div>';
	}
	$html .= '</div>';
	echo $html;
}
?>
<div class="row">
	<div class="col-xs-12 col-lg-12">
		<label>&nbsp;</label>
		<div id="dropTarget" class="drop p-lg text-center" style="border: 2px dotted #ccc; text-">
			<!--<button id="documento" class="btn btn-outline btn-default align-center {{fileClassBtn}}" type="button" ng-bind-html="fileBtn">Seleccionar</button>-->
			
			<span class="btn btn-outline btn-default align-center {{fileClassBtn}} fileinput-button">
		        <span ng-bind-html="fileBtn">Seleccionar</span>
		        <!-- The file input field used as target for the file upload widget -->
		        <input id="documento" type="file" name="documentos[]" class="fileinput-button" multiple>
		    </span>
			
			<b>o Arrastre el archivo aqui</b>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
