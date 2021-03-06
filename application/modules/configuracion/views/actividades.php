<div id="wrapper">

    <?php
	Template::cargar_vista('sidebar');
	?>
    <div id="page-wrapper" class="gray-bg row">

	    <?php Template::cargar_vista('navbar'); ?>
		<div class="row border-bottom"></div>
	    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

	    <div class="wrapper wrapper-content">
	    <!-- CONTENT WRAPPER -->

	    	<div class="row">
					<?php if (Auth::has_module_access("actividades")): ?>
				   <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-list-ul fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									 <h3 class="m-t-none m-b">Administración de Tipos de Actividades</h3>
                   <a href="<?php echo base_url('configuracion/listar_tipo_actividades') ?>"> <p>Lista Tipos de Actividades</p></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>

		<!-- /END CONTENT WRAPPER -->
		</div>

	</div>
</div>

<div class="modal fade" id="optionsModal" tabindex="-1" role="dialog"
	aria-labelledby="optionsModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Opciones</h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>
