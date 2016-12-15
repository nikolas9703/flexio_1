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
                <div id="mensaje_info"></div>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div> 

				<div role="tabpanel">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">

                <ul class="nav nav-tabs" id="cuentas_tabs_tabla">
                  <li class="active"><a href="javascript:" class="filtro" data-item="0">Todas</a></li>
                  <!-- <li><a href="javascript:" data-item="1" class="filtro">Activos</a></li>
                  <li><a href="javascript:" data-item="2" class="filtro">Pasivos</a></li>
                 <li><a href="javascript:" data-item="3" class="filtro">Patrimonio</a></li>
                 <li><a href="javascript:" data-item="4" class="filtro">Ingresos</a></li>
                 <li><a href="javascript:" data-item="5" class="filtro">Gastos</a></li> -->
              </ul>
				    		<!-- JQGRID -->
				    		<?php echo modules::run('entrada_manual/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarEntradas','autocomplete'  => 'off');
echo form_open(base_url('entrada_manual/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

?>
