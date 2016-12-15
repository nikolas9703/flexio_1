<div id="wrapper">

    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

	    <div class="wrapper wrapper-content">
        <div class="row">
            <div id="mensaje_info"></div>
        </div>
	    <!-- CONTENT WRAPPER -->
          <div class="ibox border-bottom">
            <div class="ibox-title">
                <h5>Tipo de Actividad</h5>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </div>
            </div>
            <div class="ibox-content">
        	    	<div class="row">
                  <!-- JQGRID -->
                  <?php echo modules::run('configuracion/ocultotabla'); ?>
        			</div>
            </div>
    		<!-- /END CONTENT WRAPPER -->
         </div>
		</div>

	</div>
</div>
<?php echo  Modal::modalOpciones(); ?>
<?php echo  Modal::modalGeneral('Agregar Tipo de Actividad'); ?>
