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
                <?php
                  $mensaje = self::$ci->session->flashdata('mensaje');
                ?>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>

				<div role="tabpanel">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">

				    		<!-- JQGRID -->

                <!-- Opcion: Mostrar/Ocultar columnas del jQgrid -->
                <div id="jqgrid-column-togle" class="row"></div>

                <!-- Listado de Clientes -->

                	<div class="NoRecords text-center lead"></div>

                	<!-- the grid table -->
                	<table class="table table-striped" id="EmpresaGrid"></table>

                	<!-- pager definition -->
                	<div id="pager_empresa"></div>

                <!-- /Listado de Clientes -->

				    		<!-- /JQGRID -->
								<?php
		                $formAttr = array('name'=>'formOptions', 'method'=> 'post','id' => 'formOptions','autocomplete' => 'off');
                      echo form_open("", $formAttr);
		            ?>
								<?php echo form_close(); ?>
				    	</div>

				  	</div>
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

?>
