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
				
				   <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-users fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('usuarios/politicas') ?>"><h3 class="m-t-none m-b">Administración de Usuarios</h3></a>
									<p><a href="<?php echo base_url('usuarios/listar-usuarios') ?>">Usuarios del sistema.</a></p>
									<p>&nbsp;</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				
   			 <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-archive fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('modulos/listar-modulos') ?>"><h3 class="m-t-none m-b">Administración de Módulos</h3></a>
									<p>Permite Instalar o Desinstalar</p>
									<p>Módulos a la herramienta.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-key fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('roles/listar-roles') ?>"><h3 class="m-t-none m-b">Roles y Grupos de Usuarios</h3></a>
									<p>Permite Crear Roles, Grupos y </p>
									<p>Asignar los Permisos por Módulo. </p>
								</div>
							</div>
						</div>
					</div>
				</div>
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

