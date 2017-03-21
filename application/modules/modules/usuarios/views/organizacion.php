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

                	<div class="NoRecordsEmpresa text-center lead"></div>

                	<!-- the grid table -->
                	<table class="table table-striped" id="OrganizacionGrid"></table>

                	<!-- pager definition -->
                	<div id="pager_organizacion"></div>

                <!-- /Listado de Clientes -->

				    		<!-- /JQGRID -->
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
