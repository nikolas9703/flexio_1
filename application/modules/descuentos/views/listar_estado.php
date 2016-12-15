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
									<h5>Estado de Cuenta</h5>
							        <div class="ibox-tools">
							         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
							    	</div>
								</div>
								<div class="ibox-content" style="display:block;">
									<!-- Inicia campos de Busqueda -->
							     	
							     	<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'buscarEstadoForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
							     	<div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Nombre</label>
							            	<input type="text" id="nombre" disabled class="form-control" value="<?php echo $info[0]['colaborador']['nombre'] . " " . $info[0]['colaborador']['apellido']; ?>" placeholder="">
									<input type="hidden" id="colaborador_id" value="<?php echo $info[0]['colaborador']['id'] ?>" />
									<input type="hidden" id="monto" value="<?php echo $info[0]['monto_adeudado'] ?>" />
                                                                        <input type="hidden" id="acreedor_id" value="<?php echo $info[0]['acreedor_id'] ?>" />
                                                                        
                                                                        </div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">C&eacute;dula</label>
							            	<input type="text" id="cedula" disabled class="form-control" value="<?php echo $info[0]['colaborador']['cedula']; ?>" placeholder="">
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">N° Descuento</label>
							            	<input type="text" id="codigo" disabled class="form-control" value="<?php echo $info[0]['codigo']; ?>" placeholder="">
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Acreedor</label>
							            	<input type="text" id="codigo" disabled class="form-control" value="<?php echo $info[0]['acreedores']['nombre']; ?>" placeholder="">
										</div>
                                                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Tipo de descuento</label>
							            	<input type="text" id="codigo" disabled class="form-control" value="<?php echo $info[0]['tipo_descuento']['etiqueta']; ?>" placeholder="">
										</div>
									<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Centro Contable</label>
                                                                  
                                                                        <select id="centro_contable" disabled class="form-control chosen-select"> 
                                                                            <?php 
                                                                        foreach($centro_contable AS $r){

                                                                                echo '<option value="'. $r['centro_contable']['id'] .'" selected="selected">'. $r['centro_contable']['nombre'] .'</option>';
            
                                                                        }
                                                                        
                                                                            ?>   
                                                                            
                                                                        </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cargo</label>
                                                                    
							            	<select id="cargo" disabled class="form-control chosen-select">
                                                                            
                                                                          <?php 
                                                                          
                                                                        
                                                                          
                                                                        foreach($centro_contable AS $row){

                                                                                echo '<option value="'. $row['cargo']['id'] .'">'. $row['cargo']['nombre'] .'</option>';
            
                                                                        }
                                                                        
                                                                      
                                                                        
                                                                        ?>   
                                                                           
                                                                        </select>
										</div>
										
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Ciclo</label>
							            	<select disabled class="form-control chosen-select" id="ciclo">
                                                                            <option value="<?php echo $info[0]['ciclo']['id_cat'] ?>"><?php echo $info[0]['ciclo']['etiqueta']; ?></option>
			                                		
					                        </select>
										</div>
                                                                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cantidad de descuentos actuales</label>
                                                                        
							            	<select disabled class="form-control chosen-select" id="cantidad_descuento">
			                                	<option value="<?php echo $cantidad; ?>"><?php echo $cantidad; ?></option>
			                                	
					                        </select>
										</div>
                                                                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Disponible</label>
                                                                        <div class="input-group">
							            	<span class="input-group-addon">%</span>
							      <input type="text" name="disponible" value="" class="form-control" disabled id="disponible">
                                                                        </div>
										</div>
                                                                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cuenta Pasivo</label>
                                                                       
							            	<select disabled class="form-control chosen-select" id="cuenta_pasivo">
			                                	        <option value="<?php echo $info[0]['plan_contable']['id'] ?>"><?php echo $info[0]['plan_contable']['nombre']; ?></option>

					                                </select>
										</div>
                                                                                
                                                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Desde</label>
							            	<div class="input-group">
						    			<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                        <input type="text" name="fecha_desde" value="" class="form-control fecha" readonly="readonly" id="fecha_desde" required>
                                                                        <span class="input-group-addon">a</span>
                                                                        <input type="text" name="fecha_hasta" value="" class="form-control fecha" readonly="readonly" id="fecha_hasta" required>
                                                                        </div>
										</div>                
									</div>
									<div class="row">
							        	<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
										<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" style="float:right;">
											<input type="button" id="limpiar" class="btn btn-w-m btn-block " value="Limpiar" />
										</div>
										<div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2" style="float:right;">
											<input type="button" id="Submit" class="btn btn-w-m btn-primary btn-block " value="Actualizar" />
										</div>
                                                                        
									</div>
									<?php echo form_close(); ?>									
								<!-- Termina campos de Busqueda -->
                                                                                            
                                <table id="tabla2" class="table table-bordered" style="display:none;">
                                    <div style="display:none;">
                                        <p><center><font size="8">Estado de Cuenta</font></center</p>
                                        <p></p>
                                        <p></p>
                                                                <p style="margin-bottom: 15px; width: 25%; float:left;">
                                                                    <strong>Nombre:</strong><br />
							            	<?php echo $info[0]['colaborador']['nombre'] . " " . $info[0]['colaborador']['apellido']; ?>
									  
                                                                    <strong>| C&eacute;dula:</strong><br />
							            	<?php echo $info[0]['colaborador']['cedula']; ?>
                                                                      
                                                                    <strong>| N° Descuento:</strong><br />
							            	<?php echo $info[0]['codigo']; ?>
                                                                   
										</p> 
                                                                                <p style="margin-bottom: 15px; width: 25%; float:left;">
                                                                    <strong>Acreedor:</strong><br />
							            	<?php echo $info[0]['acreedores']['nombre']; ?>
                                                                    <strong>| Tipo de descuento:</strong><br />
							            	<?php echo $info[0]['tipo_descuento']['etiqueta']; ?>
                                                                    <strong>| Centro Contable:</strong><br />
                                                                    <small id="centro_contable2"></small>
                                                                                </p>
                                                                     <p style="margin-bottom: 15px; width: 25%; float:left;">            
                                                                    <strong>Cargo:</strong><br />
							            	<small id="cargo2"></small>
                                                                   <strong>| Ciclo:</strong><br />
							            	<?php echo $info[0]['ciclo']['etiqueta']; ?>
                                                                   <strong>| Cant. de descuentos:</strong><br />
							            	<?php echo $cantidad; ?>
                                                                   
										</p>
                                                                                 <p style="margin-bottom: 15px; width: 25%; float:left;">            
                                                                    <strong>Disponible:</strong><br />
                                                                    <small id="disponible2"></small>
                                                                   <strong>| Cuenta Pasivo:</strong><br />
							            	<?php echo $info[0]['plan_contable']['nombre']; ?>
                                                                   <strong>| Desde:</strong><br />
                                                                   <small id="fecha_desde2"></small> a <small id="fecha_hasta2"></small>
                                                                   
										</p>
                                                                    
                                                                </div>    
                                <thead>
                                <tr><th>Fecha</th>
                                    <th>Monto total adeudado</th>
                                    <th>Monto por ciclo</th>
                                    <th>Saldo</th>
                                </tr>
                                </thead>
                                <tbody id="registros">
                                    
                                </tbody>
                                </table>
                                                                
								</div>
							</div>
					
                                                        
                                                        <!-- /BUSCADOR -->
                                                        
                                                        
		
				    	</div>
						
				    	
				  	</div>
				</div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarColaboradores','autocomplete'  => 'off');
echo form_open(base_url('colaboradores/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>

<?php 
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

/*echo Modal::config(array(
	"id" => "formularioEvaluacionModal",
	"size" => "lg",
	"contenido" => modules::run("colaboradores/formulario_evaluacion", array()),
	"attr" => array(
		"ng-controller" => "formularioEvaluacionController",
		"flow-init" => "",
		"flow-file-added" => 'archivoSeleccionado($file, $event, $flow)'
	)
))->html();

echo Modal::config(array(
	"id" => "entregaInventarioModal",
	"size" => "lg",
	"contenido" => modules::run("colaboradores/formulario_entrega_inventario", array()),
	"attr" => array(
		"ng-controller" => "formularioEntregaInventarioController",
	)
))->html();*/
?>

