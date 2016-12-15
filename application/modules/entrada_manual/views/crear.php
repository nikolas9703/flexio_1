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

	            <div class="row">
                	<?php
                		$info = !empty($info) ? array("info" => $info) : array();
                		echo modules::run('entrada_manual/ocultoformulario', $info);
                	?>
                </div>

              <div class="row">
                <?php
                  $comentario = !empty($comentario) ? array("comentario" => $comentario) : array();
                  if(!empty($info)){
                    	echo modules::run('entrada_manual/ocultoformulariocomentario', $comentario);
               }?>
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
