<?php
/*
echo '<h2>Consultando Antes ROWS:</h2><pre>';
            print_r($cliente_proveedor);
            echo '</pre>';
*/
?>

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
									<h5>Buscar recibos de dinero</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:none;">
									<!-- Inicia campos de Busqueda -->
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="Cliente">Cliente/Proveedor</label>
							            	<select name="cliente" class="form-control chosen-select" id="cliente">
	                                            <option value="">Seleccione</option>
	                                            <?php foreach($cliente_proveedor as $row) {?>
	                                            <option value="<?php echo $row['id_cat']; ?>"><?php echo $row['etiqueta']; ?></option>
	                                            <?php }?>
	                                       	</select>
								</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="Nombre">Nombre</label>
							            	<select id="cliente_proveedor" class="form-control select2" data-rule-required="true" aria-required="true" style="width:100%!important;">
								<option value=""></option>
							</select>
                                                                    </div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for="Narracion">Narraci&oacute;n</label>
							            	<input type="text" class="form-control" value="" id="narracion" />
                                                                    </div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Rango de monto</label>
                            <div class="form-inline">
                                <div class="form-group">
                                    <div class="input-group">
                                      <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                                      <input type="text" name="monto_desde" id="monto_desde" class="form-control">
                                      <span class="input-group-addon">a</span>
                                      <input type="text" class="form-control" name="monto_hasta" id="monto_hasta">
                                    </div>
                                </div>
                            </div>
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Rango de fecha</label>
                            <div class="form-inline">
                                <div class="form-group">
                                    <div class="input-group">
                                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                      <input type="text" name="fecha_desde" id="fecha_desde" class="form-control">
                                      <span class="input-group-addon">a</span>
                                      <input type="text" class="form-control" name="fecha_hasta" id="fecha_hasta">
                                    </div>
                                </div>
                            </div>
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
								<!-- Termina campos de Busqueda -->
								</div>
							</div>
							<!-- /BUSCADOR -->
                                                        <!-- JQGRID -->
				    		<?php echo modules::run('movimiento_monetario/ocultotabla'); ?>
				    		<!-- /JQGRID -->
				    	</div>

				  	</div>
				</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html(); ?>