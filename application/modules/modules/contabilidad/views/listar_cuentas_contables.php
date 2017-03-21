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

				<div role="tabpanel">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">

							<!-- BUSCADOR -->
							<div class="ibox border-bottom">
								<div class="ibox-title">
									<h5>Buscar cuentas contables</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
							     	<div class="row">
                      <?php
                            $formAttr = array(
                                'method'        => 'POST',
                                  'id'            => 'buscarCentroContableForm',
                                  'autocomplete'  => 'off'
                                  );
                                 echo form_open(base_url(uri_string()), $formAttr);
                                ?>
							        	<div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
							            	<label for="">Código</label>
							            	<input type="text" id="codigo" class="form-control" value="" placeholder="" autocomplete="off"  align= "left">
                       </div>
                     <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
                       <label for="">Nombre</label>
                       <input type="text" id="nombre" class="form-control" value="" placeholder="" autocomplete="off">
                     </div>
                      <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
                       <label for="">Estado</label>
                       <select id="estado" class="form-control">
                         <option value=""></option>
                         <option value="Habilitar">Habilitado</option>
                         <option value="Deshabilitar">Deshabilitado</option>
                       </select>
                     </div>
                     <?php echo form_close(); ?>
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
								<!-- Termina campos de Busqueda -->
								</div>
							</div>
							<!-- /BUSCADOR -->

				    		<!-- JQGRID -->
				    		<?php echo modules::run('contabilidad/ocultotablacuentas'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<!-- formulario para exportar-->
<?php echo modules::run('contabilidad/ocultoformularioExportarCuentas'); ?>
<?php

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
 
?>
