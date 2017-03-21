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
                <?php $mensaje = self::$ci->session->flashdata('mensaje'); ?>
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>


              <div class="ibox float-e-margins">
                <div id="ibox-content">
                    
                    
                                  <div class="ibox-title">
                                    <h5>Bitácora del contrato de alquiler No.  <?php echo  $codigo ?></h5>
                                    
                                </div>
                    
                    
                    <div id="vertical-timeline" class="vertical-container center-orientation light-timeline">
                        <?php
                          echo modules::run('contratos_alquiler/ocultotimeline');
                       ?>
                         <timeline :historial="historial"  ></timeline>
                    </div>
                </div>
            </div>

        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
