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

				<div class="row" ng-controller="AgenteFormularioController">
					<?php
					$info['form'] = array(
						'method' => 'POST',
						'id' => 'formNuevoAgente',
						'autocomplete' => 'off'
					);
						$info = !empty($info) ? array("info" => $info) : array();
						echo modules::run('agentes/ocultoformulario', $info);
					?>
				</div>
			</div>

		</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
