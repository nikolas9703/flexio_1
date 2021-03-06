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
                    <div class="alert alert-dismissable alert-danger" id="mensaje" style="display:none">
                    </div>
                </div>

				<div role="tabpanel">
					<!-- Tab panes -->
					<div class="row tab-content">
						<div role="tabpanel" class="tab-pane active" id="tabla">

							<!-- BUSCADOR -->
							<div class="ibox border-bottom">
								<div class="ibox-title">
									<h5>Buscar agente</h5>
									<div class="ibox-tools">
										<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
									</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->

									<?php
									$formAttr = array(
										'method'        => 'POST',
										'id'            => 'buscarAgenteForm',
										'autocomplete'  => 'off'
									);
									echo form_open(base_url(uri_string()), $formAttr);
									?>
									<div class="row">
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Nombre</label>
											<input type="text" id="nombre" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Cédula</label>
											<input type="text" id="identificacion" class="form-control" value="" placeholder="">
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">Tel&eacute;fono</label>
											<input type="text" id="telefono" class="form-control" value="" placeholder="">
										</div>										
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
											<label for="">E-mail</label>
											<input type="text" id="correo" class="form-control" value="" placeholder="">
										</div>
									</div>
									<div class="row">
										<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
											<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
										</div>
									</div>
									<?php echo form_close(); ?>

									<!-- Termina campos de Busqueda -->
								</div>
							</div>
							<!-- /BUSCADOR -->

							<!-- JQGRID -->

							<!-- Opcion: Mostrar/Ocultar columnas del jQgrid -->
							<div id="jqgrid-column-togle" class="row"></div>

							<!-- Listado de Clientes -->

							<div class="NoRecordsAgente text-center lead"></div>

							<!-- the grid table -->
							<table class="table table-striped" id="AgentesGrid"></table>

							<!-- pager definition -->
							<div id="pager_agentes"></div>

							<!-- /Listado de Clientes -->

							<!-- /JQGRID -->
						</div>

						<div role="tabpanel" class="tab-pane" id="grid">
							<?php Grid::visualizar_grid($grid); ?>
						</div>
					</div>
				</div>
			</div>

		</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarAgentes','autocomplete'  => 'off');
echo form_open(base_url('agentes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php
$formAttr = array('method' => 'POST', 'id' => 'cambiarEstados','autocomplete'  => 'off');
echo form_open(base_url('agentes/cambiarEstados'), $formAttr);
?>
<input type="hidden" name="idsestados" id="idsestados" value="" />
<?php echo form_close(); ?>
<?php

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
"id" => "opcionesModalEstado",  
"size" => "sm"
))->html();

?>
