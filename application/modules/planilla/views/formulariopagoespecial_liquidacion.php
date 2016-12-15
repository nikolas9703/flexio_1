 <?php 
 //Este formulario solo se usa para liquidaciones, 
 $formAttr = array(
	'method'        => 'POST',
	'id'            => 'formularioPagarLiquidaciones',
	'autocomplete'  => 'off',
	//'ng-controller' => 'subirDocumentosController'
);
echo form_open(base_url(uri_string()), $formAttr);
?><div class="row">
		<div class="alert alert-warning">	&iexcl;Atenci&oacute;n! Esta acci&oacute;n no puede ser revertida.     
                          </div>
                   </div>
   
 					 <div class="row"> <hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        		<span>&nbsp;</span><br>
   							            	<label for=""><h1>$<span id="salario_bruto">0</span></h1></label><br>
   							            	<span class="pull-left">Total de Planilla</span> 
										</div>
										<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h3>$<span id="salario_neto">0</span></h3></label><br>
 							            	<span class="pull-left">Total de salario neto</span><span id="salario_neto_porcentaje" class="pull-right"></span><br>
							            	<div class="progress">
				                                <div style="width:0%" aria-valuemax="100" id="salario_neto_progress_bar" aria-valuemin="0" aria-valuenow="0" role="progressbar" class="progress-bar progress-bar-success">
				                                    
				                                </div>
				                            </div>
					 				  </div>
					 </div> 
					 <div class="row "     ><hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        	 
  							        	 	<span>&nbsp;</span><br>
   							            	<label for=""><h1><span id="total_colaboradores">0</span></h1></label><br>
   							            	
   							            	<span class="pull-left">Colaboradores</span><br>
  							            	
										</div>
											<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h3>$<span id="deducciones">0.00</span></h3></label><br>
 							            	<span class="pull-left">Descuentos</span> <span id="deducciones_porcentaje" class="pull-right"></span><br>
							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" id="deducciones_progress_bar" aria-valuemin="0" aria-valuenow="0" role="progressbar"  class="progress-bar progress-bar-success">
				                                    
				                                </div>
				                            </div>
										</div>
 					 </div>
 					  
 					 
            </div>
            <div class="modal-footer">
            
            
            <div class="row"> 
	   		   <div class="col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
	   		   </div>
	   		   <div class="col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="confimrarCrearPagoEspecial" class="btn btn-w-m btn-primary btn-block" type="button">Confirmar</button>
	   		   </div>
	   		   </div>
             </div>
             
             
             <?php echo form_close(); ?>
        