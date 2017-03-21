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
	                'id'           => 'editarDeduccion',
	                'autocomplete' => 'off'
                );
                echo form_open(base_url(uri_string()), $formAttr);
                  ?>
                          <div class="row">
                          
                            <input type="hidden" id="deduccion_id"  name="deduccion_id"  value="<?php echo isset($info['id'])?$info['id']:0;?>"  >
                            
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                <label for="">Nombre</label>
                                <input type="text" id="nombre"  name="nombre" class="form-control"   value="<?php echo isset($info['nombre'])?$info['nombre']:'';?>" placeholder="" data-rule-required="true">
                            </div>
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
                            <label for="">Rata de colaborador</label>
                               			<div class="input-group m-b">
                                            <div class="input-group-btn" id="div_rata_colaborador">
                                                <button tabindex="-1" class="btn btn-white" type="button">Seleccione</button>  
                                                <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button" aria-expanded="true"><span class="caret"></span></button>
                                                <ul class="dropdown-menu" id="rata_colaborador_ul" >
                                                    <li><a href="#" name="monto">Monto</a></li>
                                                    <li><a href="#" name="porcentual">Porcentual</a></li>
                                                 </ul>
                                            </div>
                                            <input id="rata_colaborador" name="rata_colaborador"   value="<?php echo isset($info['rata_colaborador'])?$info['rata_colaborador']:'';?>"  type="text" class="form-control">
                                            <input id="rata_colaborador_tipo" name="rata_colaborador_tipo" type="hidden" class="form-control">
                                            <span class="input-group-addon" id="rata_simbolo">_</span>
                                           </div>
                            </div>
                           
                             <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                      <label for="">Rata de patrono</label>
                                            <div class="input-group m-b">
                                            <div class="input-group-btn" id="div_rata_patrono">
                                                <button tabindex="-1" class="btn btn-white" type="button">Seleccione</button>
                                                <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button" aria-expanded="true"><span class="caret"></span></button>
                                                <ul class="dropdown-menu" id="rata_patrono_ul" >
                                                    <li><a href="#" name="monto">Monto</a></li>
                                                    <li><a href="#" name="porcentual">Porcentual</a></li>
                                                 </ul>
                                            </div>
                                            <input id="rata_patrono" name="rata_patrono" value="<?php echo isset($info['rata_patrono'])?$info['rata_patrono']:'';?>"  type="text" class="form-control">
                                            <input id="rata_patrono_tipo" name="rata_patrono_tipo" type="hidden" class="form-control">
                                            <span class="input-group-addon" id="rata_simbolo_patrono">_</span>
                                           </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6">
                                 <label for="">Descripci&oacute;n</label>
                                 <input type="text" id="descripcion"  name="descripcion" class="form-control"  value="<?php echo isset($info['descripcion'])?$info['descripcion']:'';?>" placeholder="">
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                 <label for="">Estado</label>
                                             <select id="estado_id" name="estado_id" value="" class="form-control"   >
   												<option value="1" <?php echo ($info['estado_id'] == 1 )?"selected='selected'":'';?> >Activo</option>
												<option value="0"  <?php echo ($info['estado_id'] == 0 )?"selected='selected'":'';?> >Inactivo</option>
 		                                    </select>
                            </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3">
                               
                            </div>
                         </div>
                        
                      
    					<div class="row">
    					
    					  <div class="table-responsive"  >
							        	  
							  
                                    		
			                  <b>Constructor de Expresiones:  <?php echo $info['nombre'] ;?></b>      
			                        
 							 <table id="contructor_expresiones" class="table table-noline tabla-dinamica"  >
                                <thead id="gridHeader">
	                                <tr style="text-align: center;">
 	                                     <th width="23%">Cuando</th>
	                                     <th width="23%">Operador</th>
	                                     <th width="23%">Monto</th>
	                                     <th width="23%">Aplicar</th>
	                                     <th width="4%">&nbsp;</th>
	                                    <th width="4%">&nbsp;</th>
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
                                  	<select id="cuando_id<?php echo $i;?>"  name="constructor[<?php echo $i;?>][cuando_id]" class="form-control cuando_id" >
                                    		<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuando))
		                                        {
		                                            foreach ($cuando AS $cuand) {
														$selected =  ($contructor['cuando'] == $cuand->id_cat)?"selected='selected'":'';
 		                                                echo '<option '.$selected.' value="'. $cuand->id_cat .'">'. $cuand->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
 							      		</select>
 							      	</td>
                                   <td  class="text-navy"> 
									<select id="operador_id<?php echo $i;?>"  name="constructor[<?php echo $i;?>][operador_id]" class="form-control operador_id"  >
                                    		<option value="">Seleccione</option>
                                                        <?php
                                            if(!empty($operadores))
                                            {
                                                foreach ($operadores AS $operador)
                                                {
                                                	$selected =  ($contructor['operador'] == $operador)?" selected='selected'":'';
                                                    echo '<option '.$selected.' value="'. $operador .'">'. ucfirst($operador) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>
                                   </td>
                                        
                                   <td  class="text-navy">
                                  
 <input size="50" maxlength="50" class="form-control monto" value="<?php echo  isset($contructor['monto'])?$contructor['monto']:'';?>" id="monto" name="constructor[<?php echo $i;?>][monto]"  type="text">
                                                
                                  
                                  </td>
                                        <td  class="text-navy">
                                  
<select id="aplicar0"  name="constructor[<?php echo $i;?>][aplicar]" class="form-control aplicar"  >
                                                          <?php
                                            if(!empty($aplicar))
                                            {
                                                foreach ($aplicar AS $aplica)
                                                {
                                                	$selected =  ($contructor['aplicar'] == $aplica)?" selected='selected'":'';
                                                     echo '<option  '.$selected.' value="'. $aplica .'">'. ucfirst($aplica) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>                
                                  
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
                                	
                                	
                                	  
                                  	<td  class="text-navy"><select id="cuando_id0"  name="constructor[0][cuando_id]" class="form-control cuando_id"  >
                                    		<option value="">Seleccione</option>
		                                        <?php
		                                        if(!empty($cuando))
		                                        {
		                                            foreach ($cuando AS $cuand) {
 		                                                echo '<option  value="'. $cuand->id_cat .'">'. $cuand->etiqueta .'</option>';
		                                            }
		                                        }
		                                        ?>
 							      		</select>
 							      	</td>
                                   <td  class="text-navy">
									<select id="operador_id0"  name="constructor[0][operador_id]" class="form-control operador_id"  >
                                    		<option value="">Seleccione</option>
                                                           <?php
                                            if(!empty($operadores))
                                            {
                                                foreach ($operadores AS $operador)
                                                {
                                                 	$selected = '';
                                                    echo '<option '.$selected.' value="'. $operador .'">'. ucfirst($operador) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>
                                   </td>
                                       <td  class="text-navy">
 <input size="50" maxlength="50" class="form-control monto" id="monto" name="constructor[0][monto]"  type="text">
                                   </td>
                                   <td  class="text-navy">
                                  
<select id="aplicar0"  name="constructor[0][aplicar]" class="form-control aplicar"  >
                                                          <?php
                                            if(!empty($aplicar))
                                            {
                                                foreach ($aplicar AS $aplica)
                                                {
                                                     echo '<option   value="'. $aplica .'">'. ucfirst($aplica) .'</option>';
                                                }
                                            }
                                        ?>
 							      		</select>                
                                  
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
		                 	<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('configuracion_planilla/configuracion'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a> </div>
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
