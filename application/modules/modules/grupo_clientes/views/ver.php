<?php

$formAttr = array(
    'method' => 'POST',
    'id' => 'crearGrupoClienteForm',
    'autocomplete' => 'off'
);
echo Modal::config(array(
    "id" => "modalCrearGrupoCliente",
    "titulo" => "Editar: Grupo de Cliente",
    "contenido" => modules::run('grupo_clientes/ocultoformulario'),
    "size" => "md",
    "footer" => '<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-0 col-sm-0 col-md-4 col-lg-4">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <input type="button" id="cancelarBtn" class="btn btn-default btn-block" value="Cancelar" />
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
      <input type="button" id="guardarBtn" class="btn btn-primary btn-block" value="Guardar" />
    </div>
  </div>'
))->html();
?>
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();?>