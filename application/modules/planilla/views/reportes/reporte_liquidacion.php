<style>
a::after {
   display: none;
}
.ibox-title {
	background-color:#0070ba; 
	color:#ffffff;
}
 
</style>
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
	          
	          
                 	<?php 
                 	
                	$info = !empty($info) ? array("info" => $info) : array();
                	
                 	$nombre 	= isset($info['info']['nombre'])?$info['info']['nombre']:'';
                	$apellido 	= isset($info['info']['apellido'])?$info['info']['apellido']:'';
                	$nombre_completo = $nombre." ".$apellido;
                	
                	$centro= isset($info['info']['centro_contable']['nombre'])?$info['info']['centro_contable']['nombre']:''; 
                	$deprto = isset($info['info']['departamento']['nombre'])?'/'.$info['info']['departamento']['nombre']:'';
                	$centro_contable =  $centro.$deprto;
                 	 ?>
                 	 	<?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'verReporte',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
 			                         
			                        ?>
			                        
  <div id="formulari3333o" class="panel-group">
							<div class="panel panel-white">
								<div class="panel-heading">
									<h5 class="panel-title">
										<a href="#collapseFormulario" data-parent="#collapseFormulario" data-toggle="collapse" aria-expanded="true" class="">Datos del colaborador</a>
									</h5>
								</div>
								<div class="panel-collapse collapse in" id="collapseFormOne" aria-expanded="true" style="">
									<div class="panel-body">
									 <div class="row">
 							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Colaborador</label>
							            	 
							            	<input type="text" id="nombre" name="nombre"  class="form-control"  value="<?php echo  $nombre_completo;  ?>"   >
							            	
										</div>
										 
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">No. de colaborador</label>
							            	<input type="text" id="no_colaborador" name="no_colaborador"  class="form-control" value="<?php echo isset($info['info']['codigo'])?$info['info']['codigo']:''; ?>"   >
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">C&eacute;dula</label>
												<input type="text" id="cedula" class="form-control"  value="<?php echo isset($info['info']['cedula'])?$info['info']['cedula']:''; ?>"    >
							             
							            	
							            	</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Cargo</label>
							            	<input type="text" id="cedula" class="form-control"  value="<?php echo isset($info['info']['cargo']['nombre'])?$info['info']['cargo']['nombre']:''; ?>"   >
									 	 </div>
									</div>
									 <div class="row">
 									 
									 
 							        	<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for="">Centro Contable</label>
							            <input type="text" id="cedula" class="form-control"   value="<?php echo $centro_contable; ?>"    >
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Forma de Pago</label>
							            	   <input type="text" id="cedula" class="form-control"  value="<?php echo isset($info['info']['forma_pago']['etiqueta'])?$info['info']['forma_pago']['etiqueta']:''; ?>"   >
										</div>
								 
									</div>
									
									 
                                    
									</div>
								</div>
							</div>
 						 
 				 	 	    
						</div>	
			               
                 	  <div id="formulari3333o" class="panel-group">
							<div class="panel panel-white">
						 
								<div class="panel-collapse collapse in" id="collapseFormOne" aria-expanded="true" style="">
									<div class="panel-body">
									 <div class="row">
									 
									 
									  <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	 <div class="ibox float-e-margins">
                        <div class="ibox-title"  >
                            <h5>Ingresos</h5>
                             
                        </div>
                        <div class="ibox-content">

                         <!-- JQGRID -->
				    		<?php echo modules::run('planilla/ocultotablaingresos'); ?>
				    		
				    		<!-- /JQGRID -->

                        </div>
                    </div>
									</div>
									 
									 <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Deducciones </h5>
                             
                        </div>
                        <div class="ibox-content">

				    		<?php echo modules::run('planilla/ocultotabladeducciones'); ?>
 
                        </div>
                    </div>
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Descuentos directos </h5>
                             
                        </div>
                        <div class="ibox-content">

				    		<?php  echo modules::run('planilla/ocultotabladescuentosdirectos'); ?>
                       

                        </div>
                    </div>
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>C&aacute;lculos </h5>
                             
                        </div>
                        <div class="ibox-content">

                          <?php echo modules::run('planilla/ocultotablacalculos'); ?>

                        </div>
                    </div> 
                        <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Acumulados </h5>
                             
                        </div>
                        <div class="ibox-content">

                            <?php echo modules::run('planilla/ocultotablaacumulados'); ?>

                        </div>
                    </div> 
									</div>
										
										
										
								 
								 
									
									 
                                    
									</div>
								</div>
							</div>
 						 
 				 	 	    
						</div>	
    
                     
  			 
 				
                 </div>
                 
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php 
//echo $opciones;
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();
?>

    <?php if(preg_match("/ver/i", $_SERVER['REQUEST_URI'])){
                 	?>
  <div class="modal fade" id="pantallaPagar" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Validar Planilla </h4>
            </div>
             <div class="modal-body">
              		<div class="row">
						 <div class="alert alert-warning">
                          	&iexcl;Atenci&oacute;n! Esta acci&oacute;n no puede ser revertida.  
                          </div>
                   </div>
                   <div class="row "  >
   							       <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
 				 						<label for="">&nbsp;</label><br>
 				 						<span>&nbsp;</span><br>
							        	<span style="margin-bottom: 20px;" class="pull-left"><h4>Planilla</h4></span><span class="pull-right label label-success"><?php echo date("d/m/Y", strtotime($info['info']['rango_fecha1'])) 
							        	.' - '.date("d/m/Y", strtotime($info['info']['rango_fecha2'])) ; ?></span>
 							       </div>
								   <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><span id="salario_neto">$0.00</span></label><br>
 							            	<span class="pull-left">Total de salario Neto</span><span class="pull-right"><span id="salario_neto_porcentaje">0</span>%</span><br>
							            	<div class="progress">
				                                <div style="width: 10%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" id="progressbar_neto" role="progressbar" class="progress-bar progress-bar-success">
				                                    <span class="sr-only">50% Complete (success)</span>
				                                </div>
				                            </div>
									</div>
 					 </div>
								 	
					 <div class="row"> <hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        		<span>&nbsp;</span><br>
   							            	<label for=""><h4><span id="salario_bruto">$0.00</span></h4></label><br>
   							            	<span class="pull-left">Total de Planilla</span><span class="pull-right">100%  </span>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h4><span id="bonificaciones">$0.00</span></h4></label><br>
 							            	<span class="pull-left">Bonificaciones & comisiones</span><span class="pull-right">0%  </span><br>
							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" class="progress-bar progress-bar-success">
				                                    <span class="sr-only">35% Complete (success)</span>
				                                </div>
				                            </div>
					 				  </div>
					 </div> 
					 <div class="row "     ><hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        	 
  							        	 	<span>&nbsp;</span><br>
   							            	<label for=""><h4> <span id="total_colaboradores">0</span></h4></label><br>
   							            	
   							            	<span class="pull-left">Colaboradores</span><br>
  							            	
										</div>
											<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h4><span id="descuentos">$0.00</span></h4></label><br>
 							            	<span class="pull-left">Descuentos</span> <span class="pull-right"> <span id="descuentos_porcentaje">$0.00</span>%</span><br>
							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" id="bar_descuentos_porcentaje" class="progress-bar progress-bar-success">
				                                    
				                                </div>
				                            </div>
										</div>
 					 </div>
 					  
 					 
            </div>
            <div class="modal-footer">
            
            
            <div class="row"> 
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
	   		   </div>
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="confimrarPagar" class="btn btn-w-m btn-primary btn-block" type="button">Confirmar</button>
	   		   </div>
	   		   </div>
            
            
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="pantallaVacacion" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Validar planilla de vacaciones: <?php echo $info['info']['key'];?></h4>
            </div>
             <div class="modal-body">
              		<div class="row">
						 <div class="alert alert-warning">
                          	&iexcl;Atenci&oacute;n! Esta acci&oacute;n no puede ser revertida.  
                          </div>
                   </div>
 
								 	
					 <div class="row"> <hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        		<span>&nbsp;</span><br>
   							            	<label for=""><h4><span id="salario_bruto">$0.00</span></h4></label><br>
   							            	<span class="pull-left">Total de Planilla</span><span class="pull-right">100%  </span>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h4><span id="bonificaciones">$0.00</span></h4></label><br>
 							            	<span class="pull-left">Total de salario neto</span><span class="pull-right">0%  </span><br>
 							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" id="bar_descuentos_porcentaje" class="progress-bar progress-bar-success">
				                                    
				                                </div>
				                            </div>
 					 				  </div>
					 </div> 
					 <div class="row "     ><hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        	 
  							        	 	<span>&nbsp;</span><br>
   							            	<label for=""><h4> <span id="total_colaboradores">0</span></h4></label><br>
   							            	
   							            	<span class="pull-left">Colaboradores</span><br>
  							            	
										</div>
											<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h4><span id="descuentos">$0.00</span></h4></label><br>
 							            	<span class="pull-left">Descuentos</span> <span class="pull-right"> <span id="descuentos_porcentaje">$0.00</span>%</span><br>
							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" id="bar_descuentos_porcentaje" class="progress-bar progress-bar-success">
				                                    
				                                </div>
				                            </div>
										</div>
 					 </div>
 					  
 					 
            </div>
            <div class="modal-footer">
            
 
            <div class="row"> 
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
	   		   </div>
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="confimrarPagar" class="btn btn-w-m btn-primary btn-block" type="button">Confirmar</button>
	   		   </div>
	   		   </div>
            
            
            </div>
        </div>
    </div>
</div>
<?php } ?>


  <div class="modal fade" id="pantallaAgregarColaborador" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Agregar Colaboradores: </h4>
            </div>
             <div class="modal-body">
              
 
								 	
					 <div class="row">
										<div class="col-xs-5">
 											<select name="from[]" id="lista_colaboradores" class="form-control" size="8" multiple="multiple">
											<?php 
											
											
									$opciones = '';
 									 if(!empty($colaboradores_noactivados)){
										foreach ($colaboradores_noactivados AS $row){
 									?>
											<option value="<?php echo $row->id; ?>"><?php echo $row->apellido.', '.$row->nombre." - ".$row->cedula; ?></option>
										 
									<?php   }}?>
									 
											</select>
										</div>
										
										<div class="col-xs-2">
											<button type="button" id="lista_colaboradores_rightAll" class="btn btn-block"><i class="fa fa-forward"></i></button>
											<button type="button" id="lista_colaboradores_rightSelected" class="btn btn-block"><i class="fa fa-chevron-right"></i></button>
											<button type="button" id="lista_colaboradores_leftSelected" class="btn btn-block"><i class="fa fa-chevron-left"></i></button>
											<button type="button" id="lista_colaboradores_leftAll" class="btn btn-block"><i class="fa fa-backward"></i></button>
										</div>
										
										<div class="col-xs-5">
											<select name="to[]" id="lista_colaboradores_to" class="form-control" size="8" multiple="multiple"></select>
										</div>
									</div>
				 
            </div>
            <div class="modal-footer">
            
            
            <div class="row"> 
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
	   		   </div>
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="confimrarAgregarColaborador" class="btn btn-w-m btn-primary btn-block" type="button">Agregar</button>
	   		   </div>
	   		   </div>
            
            
            </div>
        </div>
    </div>
</div>