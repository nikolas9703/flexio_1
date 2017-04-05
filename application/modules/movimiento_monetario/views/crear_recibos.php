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
                <div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">
						
						<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" style="padding-top: 7px;">
		            		<label>Recibir dinero de </label>
						</div>
			        	<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                           
                                            <select id="categoria" class="white-bg chosen-filtro" data-placeholder="Seleccione">
								<option value=""></option>
								<?php foreach($cliente_proveedor as $row) {?>
                                                                <option value="<?php echo $row['id_cat']; ?>"><?php echo $row['etiqueta']; ?></option>
                                                                <?php }?>
							</select>
						</div>
					<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                           
                                            <select id="cliente_proveedor" class="white-bg chosen-filtro" data-rule-required="true" aria-required="true">
								<option value=""></option>
							</select>
						</div>	
						<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>
						
						<!-- Hide Nav-Tabs -->
						
					</div>
	            <div class="row" id="ocultoform">
                	<?php
                		$info = !empty($info) ? array("info" => $info) : array();
                		echo modules::run('movimiento_monetario/ocultoformulariorecibos', $info);
                	?>
                </div>

              <div class="row">
                <?php
                  $comentario = !empty($comentario) ? array("comentario" => $comentario) : array();
                  if(!empty($info)){
                    	echo modules::run('movimiento_monetario/ocultoformcomentariorecibo', $comentario);
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
