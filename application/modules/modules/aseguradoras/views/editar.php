<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
        	<div class="wrapper-content">
	            <div class="row">
	                
	            </div>

              <div class="row">
                	<?php
						echo modules::run('aseguradoras/ocultoformulario',$campos);
                	?>
               </div>
			   
			   <div class="row">
			    <?php SubpanelTabs::visualizar($subpanels); ?>
				<?php //Subpanel::visualizar_grupo_subpanel($campos['campos']['uuid_aseguradora']); ?>
				</div>
				<div>
           <?php
     //echo modules::run('aseguradoras/tabladetalles', $campos);
                 ?>
          </div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
"id" => "opcionesModal",  
"size" => "sm"
))->html();

$formAttr = array('method' => 'POST', 'id' => 'exportarContactos','autocomplete'  => 'off');

echo form_open(base_url('aseguradoras/exportarContactos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'exportarPlanesLnk','autocomplete'  => 'off');
echo form_open(base_url('planes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids2" value="" />
<?php
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'exportarSolicitudes','autocomplete'  => 'off');

echo form_open(base_url('solicitudes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_solicitudes" value="" />
<?php
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'exportarPolizas','autocomplete'  => 'off');

echo form_open(base_url('polizas/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_polizas" value="" />
<?php
echo form_close();
$formAttr = array('method' => 'POST', 'id' => 'exportarRemesasEntrantes','autocomplete'  => 'off');

echo form_open(base_url('remesas_entrantes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_remesas_entrantes" value="" />
<?php
echo form_close();
?>