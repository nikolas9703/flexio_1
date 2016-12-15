<div id="wrapper">
	<?php
	Template::cargar_vista('sidebar');
	?>
	<div id="page-wrapper" class="gray-bg row">

		<?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
		<?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

		<div class="col-lg-12">
			<div class="wrapper-content" id="crearProveedoresFormDiv">
				<div class="row">
					<div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
						<?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
					</div>
				</div>

				<div class="row">
					<?php
					$info = !empty($info) ? array("info" => $info) : array();
					echo modules::run('proveedores/ocultoformulariover', $info);
					?>
				</div>

				<div class="row" id="subpanel" style="margin-left: -15px;margin-right: -25px;">
					<?php SubpanelTabs::visualizar($uuid_proveedor); ?>
				</div>

				<!-- Comentarios -->
        <div class="row">
        	<vista_comments
          	v-if="config.vista === 'ver'"
            :config="config"
            :historial.sync="comentario.comentarios"
            :modelo="comentario.comentable_type"
            :registro_id="comentario.comentable_id"
            ></vista_comments>
					</div>
          <!-- Comentarios -->

			</div>

		</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php

echo    Modal::config(array(
	"id"    => "optionsModal",
	"size"  => "sm"
))->html();

?>
