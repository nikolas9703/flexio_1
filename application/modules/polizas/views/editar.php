<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
	$data["disabled"] = "disabled=true";
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>

                <div class="row">
                	<?php echo modules::run('polizas/editoformulario',$data); ?>
                </div>
				<div class="row">
                    <?php
                    //echo modules::run('polizas/tabladetalles');
					SubpanelTabs::visualizar($subpanels);
                    ?>
                </div>
				<div class="row">
                	<?php echo modules::run('polizas/comentaformulario',$data); ?>
                </div>
				
            </div>
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php
echo Modal::config(array(
    "id" => "documentosModal",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("Polizas/formularioModal")
))->html();

echo Modal::config(array(
    "id" => "documentosModalEditar",
    "size" => "md",
    "titulo" => "Cambiar nombre del documento",
    "contenido" => modules::run("Polizas/formularioModalEditar")
))->html();

echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
))->html();

//formulario para exportar los documentos
$formAttr = array('method' => 'POST', 'id' => 'exportarDocumentos','autocomplete'  => 'off');
echo form_open(base_url('documentos/exportarDocumentos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_documentos" value="" />
<?php
echo form_close();
?>
