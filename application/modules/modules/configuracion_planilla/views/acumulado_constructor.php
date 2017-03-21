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
                        <div role="tabpanel" class="tab-pane active" >

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Datos Generales</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content"  >
                                    <!-- Inicia campos de Busqueda -->
                                    
                                    	 
			                        
                                    <div class="row">

                                          <?php
                                          
                                           
                $formAttr = array(
	                'method'       => 'post', 
	                'id'           => 'editarAcumulado',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
                 
                 ?>
                         
                        <div class="row">
                        
                         <input type="hidden" id="acumulado_id"  name="acumulado_id"  value="<?php echo isset($info['id'])?$info['id']:0;?>"  >
                         
                            <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                <label for="">Nombre</label>
                                <input type="text" id="nombre"  name="nombre" class="form-control"  value="<?php echo isset($info['nombre'])?$info['nombre']:'';?>" placeholder="" data-rule-required="true">
                            </div>
                           <!--   <div class="form-group col-xs-12 col-sm-3 col-md-3">
                               <label for="">Tipo</label>
                                              <select id="tipo_acumulado" name="tipo_acumulado" value="" class="form-control"   >
												<option value="">Seleccione</option>
												<option value="Salario devengado" <?php echo ($info['tipo_acumulado']=='Salario devengado')?"selected='selected'":''; ?>>Salario devengado</option>
												<option value="Horas laboradas" <?php echo ($info['tipo_acumulado']=='Horas laboradas')?"selected='selected'":''; ?>>Horas laboradas</option>
 		                                    </select>
 		                                    
                            
                                  
                            </div> -->
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                             <label for="">Cuenta de pasivo</label>
                               <select id="cuenta_pasivo_id" name="cuenta_pasivo_id" value="" class="form-control"   >
                              
                              
												
												<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuentas_pasivos))
		                                        {
		                                            foreach ($cuentas_pasivos AS $cuentas) {
		 												$selected =  ($info['cuenta_pasivo']['id'] == $cuentas->id)?"selected='selected'":'';
		                                                echo '<option '.$selected.' value="'. $cuentas->id .'">'. $cuentas->nombre .'</option>';
		                                            }
		                                        }
		                                        ?>
 		                                    </select>
 		                                    
                          
                            </div>
                           
                              <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                    <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
 												<option value="1" <?php echo ($info['estado_id']==1)?"selected='selected'":''; ?>>Activo</option>
												<option value="0" <?php echo ($info['estado_id']==0)?"selected='selected'":''; ?> >Inactivo</option>
 		                                    </select>
                            </div>
                        </div>
                         <div class="row">
                            <!--   <div class="form-group col-xs-12 col-sm-3 col-md-3">
                             <label for="">M&aacute;ximo acumulable</label>
                               <div class="input-group m-b"><span class="input-group-addon">$</span> <input type="text" value="<?php echo isset($info['maximo_acumulable'])?$info['maximo_acumulable']:'';?>" name="maximo_acumulable" id="maximo_acumulable" data-inputmask="'mask': '9{1,15}.99', 'greedy' : true"  class="form-control">  </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                 <label for="">Fecha de corte</label>
                                             <div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							      <input type="input-left-addon" name="fecha_corte" value="<?php echo isset($info['fecha_corte'])?$info['fecha_corte']:'';?>" class="form-control" id="fecha_corte" readonly="readonly"   data-rule-required="true" >

							    </div>
                            </div>-->
                                <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                  <label for="">Descripci&oacute;n</label>
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control" value="<?php echo isset($info['descripcion'])?$info['descripcion']:'';?>" placeholder="">
                                      
                            </div>
                  
                        </div>
    					<div class="row">
    					
    					  <div class="table-responsive"  >
							        	  
							  
                                    		
			                  <b>Constructor de Expresiones:  <?php echo $info['nombre'] ;?></b>      
			                        
 							 <table id="contructor_expresiones" class="table table-noline tabla-dinamica"  >
                                <thead id="gridHeader">
	                                <tr style="text-align: center;">
 	                                     <th width="30%">Operador</th>
	                                     <th width="15%">C&aacute;lculo</th>
	                                     <th width="15%">&nbsp;</th>
	                                     <th width="15%">C&aacute;lculo II</th>
	                                     <th width="15%">&nbsp;</th>
	                                     <th width="5%">&nbsp;</th>
	                                    <th width="5%">&nbsp;</th>
	                                </tr>
                                </thead>
                                <tbody>
                                
                                <?php 
                                 $i = 0;
                                if(!empty($info['contructores'])){
                                	foreach($info['contructores'] as $contructor){
				
 
?>
                                		
		<tr id="contructor_expresiones<?php echo $i;?>">
		
                                   	<td  class="text-navy">
                                  	<select id="operador_id<?php echo $i;?>"  name="constructor[<?php echo $i;?>][operador_valor]" class="form-control operador_valor" >
                                    		<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($operadores))
		                                        {
		                                            foreach ($operadores AS $operador) {
														$selected =  ($contructor['operador_valor'] == $operador->valor)?"selected='selected'":'';
 		                                                echo '<option '.$selected.' value="'. $operador->valor .'">'. $operador->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
 							      		</select>
 							      	</td>
                                   <td  class="text-navy">
									<select id="tipo_calculo_uno<?php echo $i;?>"  name="constructor[<?php echo $i;?>][tipo_calculo_uno]" class="form-control tipo_calculo_uno"  >
                                    		<option value="">Seleccione</option>
                                                        <?php
                                            if(!empty($tipo_calculo))
                                            {
                                                foreach ($tipo_calculo AS $tipo)
                                                {
                                                	$selected =  ($contructor['tipo_calculo_uno'] == $tipo)?" selected='selected'":'';
                                                    echo '<option '.$selected.' value="'. $tipo .'">'. ucfirst($tipo) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>
                                   </td>
                                       <td  class="text-navy">
 <input size="50" maxlength="50" class="form-control" id="valor_calculo_uno<?php echo $i;?>" name="constructor[<?php echo $i;?>][valor_calculo_uno]"  type="text" value="<?php echo  isset($contructor['valor_calculo_uno'])?$contructor['valor_calculo_uno']:'';?>">
                                   </td>
                                   <td  class="text-navy">
                                  
<select id="tipo_calculo_dos0"  name="constructor[<?php echo $i;?>][tipo_calculo_dos]" class="form-control tipo_calculo_dos"  >
                                    		<option value="">Seleccione</option>
                                                         <?php
                                            if(!empty($tipo_calculo))
                                            {
                                                foreach ($tipo_calculo AS $tipo)
                                                {
                                                	$selected =  ($contructor['tipo_calculo_dos'] == $tipo)?" selected='selected'":'';
                                                    echo '<option '.$selected.' value="'. $tipo .'">'. ucfirst($tipo) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>                
                                  
                                  </td>
                                   <td  class="text-navy">
                                   <input size="50" maxlength="50" class="form-control" id="valor_calculo_dos<?php echo $i;?>" name="constructor[<?php echo $i;?>][valor_calculo_dos]"  type="text"  value="<?php echo  isset($contructor['valor_calculo_dos'])?$contructor['valor_calculo_dos']:'';?>">
              
                                  
                                  </td>
                                  <td   class="text-navy"> 
                                    		<button type="button" class="btn btn-default btn-block eliminarContructorBtn" agrupador="operadorUno">
                                    		<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button>
                                    		 
                                    		</td>
                                    <td > 
                                    <?php if($i==0) {?>
                                    <button type="button" class="btn btn-default btn-block agregarContructor" agrupador="operadorUno">
                                    		<i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button><?php } ?>
                                    	
                                    		</td>
                                    		
                                      <input class="form-control" id="contructor_id<?php echo $i;?>" name="constructor[<?php echo $i;?>][id]"  type="hidden"  value="<?php echo  isset($contructor['id'])?$contructor['id']:'';?>">
                                    		
                                </tr>

                                	<?php ++$i; }
                                }
                                else{ ?>
                                	<tr id="contructor_expresiones0">
                                	
                                	
                                	  
                                  	<td  class="text-navy"><select id="operador_id0"  name="constructor[0][operador_valor]" class="form-control operador_valor"  >
                                    		<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($operadores))
		                                        {
		                                            foreach ($operadores AS $operador) {
 		                                                echo '<option  value="'. $operador->valor .'">'. $operador->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
 							      		</select>
 							      	</td>
                                   <td  class="text-navy">
									<select id="tipo_calculo_uno0"  name="constructor[0][tipo_calculo_uno]" class="form-control tipo_calculo_uno"  >
                                    		<option value="">Seleccione</option>
                                                        <?php
                                            if(!empty($tipo_calculo))
                                            {
                                                foreach ($tipo_calculo AS $tipo)
                                                {
                                                    echo '<option value="'. $tipo .'">'. ucfirst($tipo) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>
                                   </td>
                                       <td  class="text-navy">
 <input size="50" maxlength="50" class="form-control tipo_calculo_uno" id="valor_calculo_uno0" name="constructor[0][valor_calculo_uno]"  type="text">
                                   </td>
                                   <td  class="text-navy">
                                  
<select id="tipo_calculo_dos0"  name="constructor[0][tipo_calculo_dos]" class="form-control tipo_calculo_dos"  >
                                    		<option value="">Seleccione</option>
                                                         <?php
                                            if(!empty($tipo_calculo))
                                            {
                                                foreach ($tipo_calculo AS $tipo)
                                                {
                                                    echo '<option value="'. $tipo .'">'. ucfirst($tipo) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>                
                                  
                                  </td>
                                   <td  class="text-navy">
                                   <input size="50" maxlength="50" class="form-control" id="valor_calculo_dos0" name="constructor[0][valor_calculo_dos]"  type="text">
              
                                  
                                  </td>
                                  <td   class="text-navy">
                                    		<button type="button" class="btn btn-default btn-block eliminarContructorBtn" agrupador="operadorUno">
                                    		<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button></td>
                                    <td > <button type="button" class="btn btn-default btn-block agregarContructor" agrupador="operadorUno">
                                    		<i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button>
                                    	
                                    	
                                    		</td>
                                    		
                                   <input class="form-control" id="contructor_id0" name="constructor[0][id]"  type="hidden"  value="0">
                                    		
                                </tr>
                                <?php }
                                ?>
                               
                                 	
                                 </tbody>
                                  
                            </table>
                                    
                                    </div>
    					
    					
    					</div>
		                 <div class="row"> 
		                 	<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
		                 	<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('configuracion_planilla/configuracion?tab-2'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a> </div>
		                 	<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
 		                 	<input name="campo[guardarFormBtn]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardarFormBtn]" type="submit">
		                 	</div>
						</div>               
		                                    
                       
                        <!-- Termina campos de Busqueda -->
                    
                  <?php echo form_close(); ?>

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

    echo    Modal::config(array(
                "id"    => "opcionesModal",
                "size"  => "sm"
            ))->html();

?>
