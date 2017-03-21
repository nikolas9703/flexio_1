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
	              <div class="row" ng-controller="toastController">
                  <?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>
 	            <div role="tabpanel" id="configuracionTabs">
				 	<!-- Tab panes -->
				  	<div class="row tab-content">
				    	<div role="tabpanel" class="tab-pane active" id="tabla">
				    	
							<div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="deducciones">Deducciones</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="true" data-targe="acumulados">Acumulados</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false"  data-targe="diasferiados">Dias Feriados</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-5" aria-expanded="false"  data-targe="recargos">Recargos</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="true" data-targe="beneficios">Beneficios</a></li>
                            <li class=""><a data-toggle="tab" href="#tab-6" aria-expanded="true" data-targe="liquidaciones">Liquidaciones</a></li>
                         </ul>
                        <div class="tab-content">
     <div id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                
                                  <?php if($permiso['permiso_deducciones']==1){  ?>
                                <div class="row">
                                
                                  
                                  <!-- BUSCADOR -->
                                  
                                    <?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearDeduccionesForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
              
                
                ?>
                   <input type="hidden" id="id_deduccion"  name="id_deduccion" value="0" >
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5><span id="titulo_form_deduccion">Datos generales</span></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    
                  
                     <div class="ibox-content m-b-sm" style="display:none;"  id="div_crear_deduccion">
                        <!-- Inicia campos de Busqueda -->
                        
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Nombre <span aria-required="true" required="">*</span></label>
                                <input type="text" id="nombre"  name="nombre" class="form-control"  value="" placeholder="" data-rule-required="true">
                            </div>
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Cuenta de pasivo  <span aria-required="true" required="">*</span></label>
                               <select id="cuenta_pasivo_id" name="cuenta_pasivo_id" value="" class="form-control"   >
                              
                              
												
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuentas_pasivos))
		                                        {
		                                            foreach ($cuentas_pasivos AS $cuentas) {
		 
		                                                echo '<option value="'. $cuentas->id .'">'. $cuentas->nombre .'</option>';
		                                            }
		                                        }
		                                        ?>
 		                                    </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                            <label for="">Rata de colaborador <span aria-required="true" required="">*</span></label>
                               			<div class="input-group m-b">
                                            <div class="input-group-btn open" id="div_rata_colaborador">
                                                <button tabindex="-1" class="btn btn-white" type="button">Seleccione</button>  
                                                <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button" aria-expanded="true"><span class="caret"></span></button>
                                                <ul class="dropdown-menu" id="rata_colaborador_ul" >
                                                    <li><a href="#" name="monto">Monto</a></li>
                                                    <li><a href="#" name="porcentual">Porcentual</a></li>
                                                 </ul>
                                            </div>
                                            <input id="rata_colaborador" name="rata_colaborador" type="text" class="form-control">
                                            <input id="rata_colaborador_tipo" name="rata_colaborador_tipo" type="hidden" class="form-control">
                                            <span class="input-group-addon" id="rata_simbolo">_</span>
                                           </div>
                            </div>
                           
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                      <label for="">Rata de patrono <span aria-required="true" required="">*</span> </label>
                                            <div class="input-group m-b">
                                            <div class="input-group-btn open" id="div_rata_patrono">
                                                <button tabindex="-1" class="btn btn-white" type="button">Seleccione</button>
                                                <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button" aria-expanded="true"><span class="caret"></span></button>
                                                <ul class="dropdown-menu" id="rata_patrono_ul" >
                                                    <li><a href="#" name="monto">Monto</a></li>
                                                    <li><a href="#" name="porcentual">Porcentual</a></li>
                                                 </ul>
                                            </div>
                                            <input id="rata_patrono" name="rata_patrono" type="text" class="form-control">
                                            <input id="rata_patrono_tipo" name="rata_patrono_tipo" type="hidden" class="form-control">
                                            <span class="input-group-addon" id="rata_simbolo_patrono">_</span>
                                           </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                 <label for="">Descripci&oacute;n</label>
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control" value="" placeholder="">
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                 <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
 												<option value="1">Activo</option>
												<option value="0">Inactivo</option>
 		                                    </select>
                            </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3">
                               
                            </div>
                         </div>
    					
		                                
		                                    
                        <div class="row">
								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarDeduccionBtn" class="btn btn-w-m btn-default" value="Cancelar" />
									<input type="button" id="guardarDeduccionBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
 							</div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                  <?php echo form_close(); ?>
                <!-- /BUSCADOR -->
                        <?php echo modules::run('configuracion_planilla/ocultotabladeducciones'); ?>
      
                                </div>
                                  <?php }else{ ?>
                                 <div class="alert alert-danger">
                               Usted no cuenta con los permisos para entrar a deducciones.
                            </div>
                                  	 
                                  
                                  <?php } ?>
                                    
                                </div>
                            </div>
                            
                             <div id="tab-2" class="tab-pane">
                                <div class="panel-body">
                                
                                  <?php if($permiso['permiso_acumulados']==1){  ?>
                                <div class="row">
                                
                                  
                                  <!-- BUSCADOR -->
                                  
                                    <?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearAcumuladosForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
              
                
                ?>
                   <input type="hidden" id="id_acumulado"  name="id_acumulado" value="0" >
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5><span id="titulo_form_acumulado">Datos generales</span></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    
                  
                     <div class="ibox-content m-b-sm" style="display:none;"  id="div_crear_acumulado">
                        <!-- Inicia campos de Busqueda -->
                        
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                <label for="">Nombre <span aria-required="true" required="">*</span></label>
                                <input type="text" id="nombre"  name="nombre" class="form-control"  value="" placeholder="" data-rule-required="true">
                            </div>
                             <!--  <div class="form-group col-xs-12 col-sm-3 col-md-3">
                               <label for="">Tipo</label>
                                              <select id="tipo_acumulado" name="tipo_acumulado" value="" class="form-control"   >
												<option value="">Seleccione</option>
												<option value="Salario devengado">Salario devengado</option>
												<option value="Horas laboradas">Horas laboradas</option>
 		                                    </select>
 		                                    
                            
                                  
                            </div> -->
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                             <label for="">Cuenta de pasivo <span aria-required="true" required="">*</span></label>
                               <select id="cuenta_pasivo_id" name="cuenta_pasivo_id" value="" class="form-control"   >
                              
                              
												
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuentas_pasivos))
		                                        {
		                                            foreach ($cuentas_pasivos AS $cuentas) {
		 
		                                                echo '<option value="'. $cuentas->id .'">'. $cuentas->nombre .'</option>';
		                                            }
		                                        }
		                                        ?>
 		                                    </select>
 		                                    
                          
                            </div>
                           
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                    <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
 												<option value="1">Activo</option>
												<option value="0">Inactivo</option>
 		                                    </select>
                            </div>
                        </div>
                         <div class="row">
                          <!--   <div class="form-group col-xs-12 col-sm-3 col-md-3">
                             <label for="">M&aacute;ximo acumulable</label>
                               <div class="input-group m-b"><span class="input-group-addon">$</span> <input type="text" name="maximo_acumulable" id="maximo_acumulable" data-inputmask="'mask': '9{1,15}.99', 'greedy' : true"  class="form-control">  </div>
                            </div> 
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                 <label for="">Fecha de corte</label>
                                             <div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							      <input type="input-left-addon" name="fecha_corte" value="" class="form-control" id="fecha_corte" readonly="readonly"   data-rule-required="true" >

							    </div>
                            </div>-->
                                <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                  <label for="">Descripci&oacute;n</label>
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control" value="" placeholder="">
                                      
                            </div>
                  
                        </div>
    					
		                                
		                                    
                        <div class="row">
								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarAcumuladoBtn" class="btn btn-w-m btn-default" value="Cancelar" />
									<input type="button" id="guardarAcumuladoBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
 							</div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                  <?php echo form_close(); ?>
                <!-- /BUSCADOR -->
                        <?php echo modules::run('configuracion_planilla/ocultotablaacumulados'); ?>
      
                                </div>
                                  <?php }else{ ?>
                                 <div class="alert alert-danger">
                               Usted no cuenta con los permisos para entrar a acumulados.
                            </div>
                                  	 
                                  
                                  <?php } ?>
                                    
                                </div>
                            </div>
                            
       <div id="tab-3" class="tab-pane">
                                <div class="panel-body">
                                
                                  <?php if($permiso['permiso_beneficios']==1){  ?>
                                <div class="row">
                                
                                  
                                  <!-- BUSCADOR -->
                                  
                                    <?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearBeneficioForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
              
                
                ?>
                   <input type="hidden" id="id_beneficio"  name="id_beneficio" value="0" >
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5><span id="titulo_form">Datos generales</span></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    
                  
                     <div class="ibox-content m-b-sm" style="display:none;"  id="div_crear_beneficio">
                        <!-- Inicia campos de Busqueda -->
                        
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Nombre <span aria-required="true" required="">*</span></label>
                                <input type="text" id="nombre"  name="nombre" class="form-control"  value="" placeholder="" data-rule-required="true">
                            </div>
                                  <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Cuenta de pasivo</label>
                              <select id="cuenta_pasivo_id" name="cuenta_pasivo_id" value="" class="form-control"   >
 												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuentas_pasivos))
		                                        {
		                                            foreach ($cuentas_pasivos AS $cuentas) {
		 
		                                                echo '<option value="'. $cuentas->id .'">'. $cuentas->nombre .'</option>';
		                                            }
		                                        }
		                                        ?>
 		                                    </select>
                            </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Modificador <span aria-required="true" required="">*</span></label>
                               <div class="input-group" >
							      <input type="input-right-addon" name="modificador_actual" value="" class="form-control"  data-inputmask="'mask': '9{1,15}.99', 'greedy' : true" id="modificador_actual" required>
								  <span class="input-group-addon">%</span>
							    </div>
                            </div>
                              <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
 												<option value="1">Activo</option>
												<option value="0">Inactivo</option>
 		                                    </select>
                            </div>
                            
                           
                      
                         
                        </div>
                         <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Deducciones aplicables</label>
                                            <select id="id_deducciones" name="deducciones[]" value="" class="form-control" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($deducciones))
		                                        {
		                                            foreach ($deducciones AS $deduccion) {
		 
		                                                echo '<option value="'. $deduccion['id'] .'">'. $deduccion['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Acumulados aplicables</label>
                                             <select id="id_acumulados" name="acumulados[]" value="" class="form-control" multiple="multitple" size="1"  data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($acumulados))
		                                        {
		                                            foreach ($acumulados AS $acumulado) {
		 
		                                                echo '<option value="'. $acumulado['id'] .'">'. $acumulado['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
                            </div>
                              
                               <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                <label for="">Descripci&oacute;n</label>
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control" value="" placeholder="">
                            </div>
                            
                             
                         </div>
    					
		                                    
		                                    
                        <div class="row">
								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarFormBtn" class="btn btn-w-m btn-default" value="Cancelar" />
									<input type="button" id="guardarBeneficioBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
 							</div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                  <?php echo form_close(); ?>
                <!-- /BUSCADOR -->
                        <?php echo modules::run('configuracion_planilla/ocultotablabeneficios'); ?>
      
                                </div>
                                  <?php }else{ ?>
                                 <div class="alert alert-danger">
                               Usted no cuenta con los permisos para entrar a beneficios.
                            </div>
                                  	 
                                  
                                  <?php } ?>
                                    
                                </div>
                            </div>
                            
                             
                            
                            <div id="tab-4" class="tab-pane">
                                <div class="panel-body">
                                <?php if($permiso['permiso_feriados']==1){ ?>
                                     <div class="row">
                                 
                                  
                                  <!-- BUSCADOR -->
                                  
                                    <?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearDiaFeriadoForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
              
                
                ?>
                <input type="hidden" id="id_diaferiado"  name="id_diaferiado" value="0" >
                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5><span id="titulo_form_feriados"> Datos generales</span></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    
                  
                     <div class="ibox-content m-b-sm" style="display:none;"  id="div_crear_diaferiado">
                        <!-- Inicia campos de Busqueda -->
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Nombre <span aria-required="true" required="">*</span></label>
                                <input type="text" id="fecha_nombre"  name="fecha_nombre" class="form-control"  value="" placeholder="" data-rule-required="true">
                            </div>
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                             <label for="">Fecha oficial</label>
                             
                             <div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							      <input type="input-left-addon" name="fecha_fecha_oficial" value="" class="form-control fecha-fecha-oficial" readonly="readonly" id="fecha_fecha_oficial"  data-rule-required="true" >

							    </div>
                            </div>
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Horas no laboradas <span aria-required="true" required="">*</span></label>
                                <input type="text" id="horas_no_laboradas"   data-inputmask="'mask': '9{1,15}', 'greedy' : true" name="horas_no_laboradas" class="form-control"  value="" placeholder="" data-rule-required="true">
	                              
                            </div>
                            
                               <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Cuenta de Pasivo</label>
                                             <select id="cuenta_pasivo_id" name="cuenta_pasivo_id" value="" class="form-control" >
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuentas_pasivos))
		                                        {
		                                            foreach ($cuentas_pasivos AS $cuentas) {
		 
		                                                echo '<option value="'. $cuentas->id .'">'. $cuentas->nombre .'</option>';
		                                            }
		                                        }
		                                        ?>
 		                                    </select>
                            </div>
                            
                            
                         </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Deducciones aplicables</label>
                                            <select id="id_deducciones" name="deducciones[]" value="" class="form-control" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($deducciones))
		                                        {
		                                            foreach ($deducciones AS $deduccion) {
		 
		                                                echo '<option value="'. $deduccion['id'] .'">'. $deduccion['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Acumulados aplicables</label>
                                             <select id="id_acumulados" name="acumulados[]" value="" class="form-control" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($acumulados))
		                                        {
		                                            foreach ($acumulados AS $acumulado) {
		 
		                                                echo '<option value="'. $acumulado['id'] .'">'. $acumulado['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
                            </div>
                           
                              <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                <label for="">Descripci&oacute;n</label>
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control" value="" placeholder="">
                            </div>
                            
                          
                     
                        </div>
                          <div class="row">
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
 												<option value="1">Activo</option>
												<option value="0">Inactivo</option>
 		                                    </select>
                            </div>
                          </div>
                        <div class="row">
                        <div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarFeriadoBtn" class="btn btn-w-m btn-default" value="Cancelar" />
									<input type="button" id="guardarFormDiasFeriadosBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
							
 							</div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                  <?php echo form_close(); ?>
                <!-- /BUSCADOR -->
                <!-- 
                			 <div class="row">
 								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
 									<button type="button" id="GenerarDiasFeriados" class="btn btn-w-m btn-primary" />Generar Dias Feriados</button>
								</div></br>
 							</div> -->
 							
 							 
                        <?php echo modules::run('configuracion_planilla/ocultotabladiasferiados'); ?>
      
                                </div>
                                
                                <?php } else { ?>
                                <div class="alert alert-danger">
                               Usted no cuenta con los permisos para entrar a dias feriados.
                            </div>
                                	 
                                <?php } ?>
                                </div>
                            </div>
                            <div id="tab-5" class="tab-pane">
                               <div class="panel-body">
                               <?php if($permiso['permiso_recargos']==1){ ?>
                               
                                     <div class="row">
                                
                                  
                                  <!-- BUSCADOR -->
                                  
                                    <?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearRecargoForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
              
                
                ?>
                                <input type="hidden" id="id_recargo"  name="id_recargo" value="0" >
                
                 <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5><span id="titulo_form_recargos"> Datos generales</span></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>
                    
                  
                     <div class="ibox-content m-b-sm" style="display:none;"  id="div_crear_recargo">
                        <!-- Inicia campos de Busqueda -->
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Abreviatura para hoja de tiempo <span aria-required="true" required="">*</span></label>
                                <input type="text" id="nombre"  name="nombre" class="form-control"  value="" placeholder="" data-rule-required="true">
                            </div>
                               <div class="form-group col-xs-12 col-sm-3 col-md-3">
                             <label for="">% por hora</label> 
                              <div class="input-group">
 							      <input type="input-left-addon" name="porcentaje_hora"   value="" class="form-control"  id="porcentaje_hora"  >
								<span class="input-group-addon">%</span>
							    </div>
                             </div>
                             
                            <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                <label for="">Descripci&oacute;n</label> 
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control" value="" placeholder="">
                            </div>
                         
                             
                        </div>
                               <div class="row">
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                     <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
 												<option value="1">Activo</option>
												<option value="0">Inactivo</option>
 		                                    </select>
                            </div>
                              
                            
                         
                             
                        </div>
                         <div class="row">
                        		<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarRecargoBtn" class="btn btn-w-m btn-default" value="Cancelar" />
                        			<input type="button" id="guardarFormRecargoBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
  							</div>
                        <!-- Termina campos de Busqueda -->
                    </div>
                </div>
                  <?php echo form_close(); ?>
                <!-- /BUSCADOR -->
                
                 <div class="row">
 								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
								</div>
 							</div>
 							
 							
                        <?php echo modules::run('configuracion_planilla/ocultotablarecargos'); ?>
      
                                </div>
                                
                                <?php } else{ ?>
                              <div class="alert alert-danger">
                               Usted no cuenta con los permisos para entrar a dias recargos.
                            </div>
                                <?php } ?>
                                </div>
                            </div>
                            
                           <div id="tab-6" class="tab-pane">
                               <div class="panel-body">
                               <?php //if($permiso['permiso_liquidaciones']==1){ ?>
                               
                                     <div class="row">
                                
                                  
                                  <!-- BUSCADOR -->
                                  
                                    <?php
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'crearLiquidacionForm',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
              
                
                ?>
                
                
                 	<input type="hidden" id="id_liquidacion"  name="liquidacion[id]" value="0" >
                
                 <div class="ibox-content">

                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            
                            <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingThree">
                                        <h4 class="panel-title">
                                             <a class="" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                                Datos Generales
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree" aria-expanded="true">
                                        <div class="panel-body">
                                           
                                           <div class="form-group col-xs-12 col-sm-3 col-md-3">
                               				 <label for="">Tipo de Liquidaci&oacute;n <span aria-required="true" required="">*</span></label>
                              					 <select id="tipo_liquidacion" name="liquidacion[tipo_liquidacion]" value="" class="form-control"  data-rule-required="true"  >
 												<option value="">Seleccione</option>
		                                        <?php  
		                                        if(!empty($tipo_liquidaciones))
		                                        {
		                                            foreach ($tipo_liquidaciones AS $liquidacion) {
		 
		                                                echo '<option value="'. $liquidacion->id_cat .'">'. $liquidacion->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
 		                                    </select>
                            			</div>
                                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                               				 <label for="">Estado </label>
                              					 <select id="estado_id" name="liquidacion[estado_id]" value="" class="form-control"   >
 												<option value="1">Activo</option>
		                                        <option value="0">Inactivo</option>
 		                                    </select>
                            			</div>
                                           
                                           
                                           
                                           
                                           
                                           
                                        </div>
                                    </div>
                                </div>
                            
                            
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                             <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                                Pagos aplicables
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                           
                                        
                                                           <div class="row">
    					
    					  <div class="table-responsive"  >
							        	 
			                        
 							 	<table id="pagos_aplicables" class="table table-noline tabla-dinamica"  >
	                                <thead id="gridHeader">
		                                <tr style="text-align: center;">
	 	                                     <th width="40%">Pagos aplicables</th>
		                                     <th width="40%">Deducciones aplicables</th>
		                                     <th width="10%">&nbsp;</th>
		                                     <th width="10%">&nbsp;</th>
	                                    </tr>
	                                </thead>
                                <tbody>
                             
                                		
								<tr id="pago_aplicable_fila0">
                                    <td  class="text-navy">  
 	                                  	<select  data-rule-required="true"  id="pagos_aplicables_normales0"  name="liquidacion_pago[pago][0][id]" class="form-control pagos_aplicables_normales"  >
	                                    		<option value="">Seleccione</option>
 	                                    		<?php  
	                                    		
	                                    		 
		                                        if(!empty($pagos_aplicables_normales))
		                                        {
		                                            foreach ($pagos_aplicables_normales AS $pago) {
		 
		                                                echo '<option value="'. $pago->id_cat .'">'. $pago->etiqueta .'</option>';
		                                            }
		                                        } 
		                                        ?>
 	 							      	</select> </td>
 	 							      	
 	 							      		<td class="hide">
	                              
	                              
	                             	 <input type="hidden" id="id_pago0" class="id_pago" name="liquidacion_pago[pago][0][id_registro]" value="0" >
	                              						      	</td>
	                              						      	
                                   <td  class="text-navy">
                       
  		                                     <select id="deducciones_pagos_normales0" name="liquidacion_pago[pago][0][deduccion][]" value="" class="form-control deducciones_pagos_normales" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($deducciones))
		                                        {
		                                            foreach ($deducciones AS $deduccion) {
		 
		                                                echo '<option value="'. $deduccion['id'] .'">'. $deduccion['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
		                                    
                                   
  								   </td>
                                  <td class="text-navy"> 
                                     		<button type="button" class="btn btn-default btn-block eliminarContructorBtn" agrupador="operadorUno">
                                    		<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button>                                    		</td>
                                    		<td ><button type="button" class="btn btn-default btn-block agregarFila" agrupador="operadorUno">
                                    		<i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button></td>
                                    		
                                    		
                              	  </tr>

                                	 
                                 </tbody>
                            </table>
                                    
  </div>
    					
    					
    					</div>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingTwo">
                                        <h4 class="panel-title">
                                             <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                               Acumulados aplicables
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                            
                                            
                                            <div class="row">
    					
    					  <div class="table-responsive"  >
							        	 
			                        
 							 	<table id="acumulados_aplicables" class="table table-noline tabla-dinamica"  >
	                                <thead id="gridHeader">
		                                <tr style="text-align: center;">
	 	                                     <th width="40%">Pagos aplicables</th>
		                                     <th width="40%">Deducciones aplicables</th>
		                                     <th width="10%">&nbsp;</th>
		                                     <th width="10%">&nbsp;</th>
	                                    </tr>
	                                </thead>
                                <tbody>
                             
                                		
								<tr id="acumulados_aplicables_fila0">
                                    <td  class="text-navy">
 	                                  	<select id="pagos_aplicables_acumulados0"  name="liquidacion_acumulado[pago][0][id]" class="form-control pagos_aplicables_acumulados" >
	                                    		<option value="">Seleccione</option>
 	                                    		<?php  
	                                    		
	                                    		 
		                                        if(!empty($pagos_aplicables_acumulados))
		                                        {
		                                            foreach ($pagos_aplicables_acumulados AS $pago) {
		 
		                                                echo '<option value="'. $pago->id_cat .'">'. $pago->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
 	 							      	</select> 							      	</td>
 	 							      	
 	 							      	
 	 							      	<td class="hide">
	                              
	                              
	                             	 <input type="hidden" id="id_acumulado0" class="id_acumulado" name="liquidacion_acumulado[pago][0][id_registro]" value="0" >
	                              						      	</td>
 	 							      	
 	 							      	
 	 							      	
                                   <td  class="text-navy">
                                  		 <select id="deducciones_pagos_acumulados0" name="liquidacion_acumulado[pago][0][deduccion][]" value="" class="form-control deducciones_pagos_acumulados" multiple="multitple" size="1" data-placeholder="Seleccione">
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($deducciones))
		                                        {
		                                            foreach ($deducciones AS $deduccion) {
		 
		                                                echo '<option value="'. $deduccion['id'] .'">'. $deduccion['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
  								   </td>
                                  <td class="text-navy"> 
                                  			
                                    		<button type="button" class="btn btn-default btn-block eliminarContructorBtn" agrupador="operadorUno">
                                    		<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button>                                    		</td>
                                    		<td ><button type="button" class="btn btn-default btn-block agregarFila" agrupador="operadorUno">
                                    		<i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button></td>
                                     		
                              	  </tr>

                                	 
                                 </tbody>
                            </table>
                                    
  </div>
    					
    					
    					</div>
                                            
                                            
                                            
                                            
                                            
                                            
                                           
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
     <div class="row">
                        		<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
									<input type="button" id="cancelarLiquidacionBtn" class="btn btn-w-m btn-default" value="Cancelar" />
                        			<input type="button" id="guardarFormLiquidacionBtn" class="btn btn-w-m btn-primary" value="Guardar" />
								</div>
  							</div>


                        </div>
                        
                  <?php echo form_close(); ?>
                <!-- /BUSCADOR -->
                
                 <div class="row">
 								<div class="pull-right col-md-3 col-sm-6 col-xs-12" style=" text-align: right;">
								</div>
 							</div>
 							
                         <?php echo modules::run('configuracion_planilla/ocultotablaliquidacion'); ?>
                                 </div>
                                
                                <?php /*} else{ */?>
                              <!-- <div class="alert alert-danger">
                               Usted no cuenta con los permisos para entrar a dias recargos.
                            </div> -->
                                <?php //} ?>
                                </div>
                            </div>
                            
                        </div>


                    </div>
                </div>
               
            </div>
 				    	</div>
 				  	</div>
 				  	 <select id="pagos_aplicables_original"  style="display: none;"  >
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($pagos_aplicables_normales))
		                                        {
		                                            foreach ($pagos_aplicables_normales AS $pagos_aplicable) {
		 
		                                                echo '<option value="'. $pagos_aplicable->id_cat .'">'. $pagos_aplicable->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
		                                    
		                                     <select id="pagos_acumulados_original"  style="display: none;"  >
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($pagos_aplicables_acumulados))
		                                        {
		                                            foreach ($pagos_aplicables_acumulados AS $pagos_aplicable) {
		 
		                                                echo '<option value="'. $pagos_aplicable->id_cat .'">'. $pagos_aplicable->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
		                                    
 				  	   <select id="id_deducciones_original"  value="" class="form-control"  size="1" data-placeholder="Seleccione" style="display: none;"  >
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($deducciones))
		                                        {
		                                            foreach ($deducciones AS $deduccion) {
		 
		                                                echo '<option value="'. $deduccion['id'] .'">'. $deduccion['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
		                                   <select id="id_acumulados_original"  value="" class="form-control"  size="1" data-placeholder="Seleccione" style="display: none;"    >
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($acumulados))
		                                        {
		                                            foreach ($acumulados AS $acumulado) {
		 
		                                                echo '<option value="'. $acumulado['id'] .'">'. $acumulado['nombre'] .'</option>';
		                                            }
		                                        }
		                                        ?>
		                                    </select>
		                                    
		                                    
				</div>
         	</div>
        	 
    	</div><!-- cierra .col-lg-12 --> 
	</div><!-- cierra #page-wrapper --> 
</div><!-- cierra #wrapper -->
  
  
<?php 

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

?>
