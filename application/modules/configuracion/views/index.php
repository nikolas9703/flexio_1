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
	    	<?php
             if (Auth::has_module_access( "actividades" )) :
            ?>
 				 <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-tty fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									 <h3 class="m-t-none m-b">Actividades</h3> 
									<p>Registro de activiades relacionadas a módulos.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php
             //if (Auth::has_module_access( "casos" )) :
            ?>
			 <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-flag fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<h3 class="m-t-none m-b">Casos</h3>
									<p>Módulo para soporte post-venta.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php //endif; ?>
				<?php
             if (Auth::has_module_access( "configuracion" )) :
            ?>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-cogs fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('configuracion/grupo') ?>"><h3 class="m-t-none m-b">Configuración de Sistema</h3></a>
									<p>Administrar  Usuarios, Roles y Políticas del Sistema.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php
             if (Auth::has_module_access( "documentos" )) :
            ?>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-copy fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<h3 class="m-t-none m-b">Documentos</h3>
									<p>Repositorio del sistema para almacenar los archivos.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( Auth::has_module_access ( "proyectos" ) || Auth::has_module_access ( "propiedades" ) ): ?>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-cubes fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('configuracion/inventario') ?>"><h3 class="m-t-none m-b">Inventario</h3></a>
									<p>Gestión de Proyectos y Propiedades.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php
           //  if (Auth::has_module_access( "actividades" )) :
            ?>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-bell fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<h3 class="m-t-none m-b">Notificaciones</h3>
									<p>Habiliar y deshabilitar Notificaciones del Sistema.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php //endif; ?>
				<?php
             if (Auth::has_module_access( "agentes" )) :
            ?>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-book fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('configuracion/proveedores') ?>"><h3 class="m-t-none m-b">Proveedores</h3></a>
									<p>Agentes.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php
			if (Auth::has_module_access("clientes") || Auth::has_module_access ("clientes_potenciales") || Auth::has_module_access("contactos") || Auth::has_module_access("oportunidades")) :
			?>
				
				 <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4">
					<div class="ibox float-e-margins">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-3">
									<p class="pull-left" style="margin-right:10px;">
										<i class="fa fa-folder-open fa-5x"></i>
									</p>
								</div>
								<div class="col-sm-9">
									<a href="<?php echo base_url('configuracion/ventas') ?>"><h3 class="m-t-none m-b">Ventas</h3></a>
									<p>Clientes, Contactos, Clientes Potenciales & Oportunidades.</p>
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

