<style>
form div.table-responsive{
    overflow: scroll;
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
	           
	            
 	            $rango_fecha1 = explode(" ", $info['rango_fecha1']);
 	            $fecha_inicio1 = date("d/m/Y", strtotime($rango_fecha1[0]));
 	            
 	            $rango_fecha2 = explode(" ", $info['rango_fecha2']);
 	            $fecha_inicio2 = date("d/m/Y", strtotime($rango_fecha2[0]));
 	            
 	            $fechas = $fecha_inicio1.' a '.$fecha_inicio2;
 	            
 	            $centro = isset($info['centro']['nombre'])?$info['centro']['nombre']:'';
 	            $subcentro = isset($info['subcentro']['nombre'])?'/'.$info['subcentro']['nombre']:'';
 	            $area_negocios = isset($info['area_negocios']['nombre'])?'/'.$info['area_negocios']['nombre']:'';
 	            $centro_contable = $centro.$subcentro.$area_negocios;
 	            ?>
 	           
 	          
 	             
  <div id="accordion" class="panel-group">
							<div class="panel panel-white">
								<div class="panel-heading">
									<h5 class="panel-title">
										<a href="#collapseOne" data-parent="#accordion" data-toggle="collapse" aria-expanded="true" class="">Informaci&oacute;n de la planilla</a>
									</h5>
								</div>
								<div class="panel-collapse collapse in" id="collapseOne" aria-expanded="true" style="">
									<div class="panel-body">
									 <div class="row">
							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">N&uacute;mero de planilla</label>
							            	<p  class="form-control-static"><?php echo $info['identificador'].$info['semana'].$info['ano'].$info['secuencial'];?></p>
 										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Centro Contable</label>
							            	<p  class="form-control-static"><?php echo $centro_contable;?></p>
 										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Rango de fechas</label>
							            	<p  class="form-control-static"><?php echo $fechas;?></p>
 										</div>
 										
 										 
 										 <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
 							            	<input type="button" id="validarFinalBtn" class="btn btn-primary btn-block" value="Validar" style="display:none;" />
 										</div>
 										
 										
	        
										 
									</div>
								</div>
							</div>
 						</div>		
  </div> 
   
   
  <div id="#accordion2" class="panel-group">
							<div class="panel panel-white">
								 
								<div class="panel-collapse collapse in" id="collapseTwo" aria-expanded="true" style="">
									<div class="panel-body">
									 <div class="row">
									 <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2"> 
									 <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title"  style="background-color: #0070BA; color:#ffffff;">
                                    <h5>Colaboradores</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                     
			                        
                                    <div class="row">

                                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
							            	<label for="">C&eacute;dula</label>
											 <input type="text" id="cedula" class="form-control"  value=""   >
										</div>

                                        
                                    
                                    </div>
                                 
                                    <div class="row">
                                         <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <input type="button" id="searchBtnCol" class="btn btn-default btn-block" value="Filtrar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <input type="button" id="clearBtnCol" class="btn btn-default btn-block" value="Limpiar" />
                                        </div>
                                    </div>
                                   
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <!-- /BUSCADOR -->
                            
                            	<?php
 							      echo modules::run('planilla/ocultotablacolaboradorentry', isset( $info) ?$info:array());?>
 							     
							        	 </div>
							        	 <?php
			                        $formAttr = array(
			                            'method'        => 'POST', 
			                            'id'            => 'guardarEntradaHorasForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>
			                        
							        	 <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10"> 
							        	
 							        	 
							        	 <div class="ibox border-bottom">
			                                <div class="ibox-title"  style="background-color: #0070BA; color:#ffffff;">
			                                    <h5>Semana <?php echo (int)date("W");?></h5>
			                                    <div class="ibox-tools">
			                                      Colaborador:  <span id='colaborador_nombre1'></span> <input type="hidden" name="planilla_id" value="<?php echo $info['id']; ?>" />  
			                                      <input type="hidden" name="colaborador_id" id="colaborador_id"  />
			                                      <input type="hidden" name="edicion" id="edicion" />
			                                    </div>
			                                </div>
                                		</div>
							        	 <!-- Aqui empieza la primera tabla  -->
 			                        
							        	  <div class="table-responsive" style="overflow-y: auto;height:300px;overflow:scroll;">
							        	  
							  
                                    		
			                        
			                        
 							 <table id="semanaUnoTable" class="table table-noline tabla-dinamica"  >
                                <thead id="gridHeader">
                                <tr style="text-align: center;">
                                	<th   style="display: none;"><b>ID</b></th>
                                    <th  width="12%"><b>Centro Contable</b></th>
                                    <th  width="12%"><b>Base</b><span required="" aria-required="true">*</span></th>
                                    <th  width="12%"><b>Actividad</b><span required="" aria-required="true">*</span></th>
                                    <th  width="12%"><b>Beneficio</b></th>
                                    <th  width="14%"><b>Cuenta de Gasto</b><span required="" aria-required="true">*</span></th>
                                    <th width="5%"><b>L  </br><?php echo $semana_actual[1];?></b></th>
                                    <th width="5%"><b>M  </br><?php echo $semana_actual[2];?></b></th>
                                    <th width="5%"><b>Mi </br><?php echo $semana_actual[3];?></b></th>
                                    <th width="5%"><b>J  </br><?php echo $semana_actual[4];?></b></th>
                                    <th width="5%"><b>V  </br><?php echo $semana_actual[5];?></b></th>
                                    <th width="5%"><b>S  </br><?php echo $semana_actual[6];?></b></th>
                                    <th width="5%"><b>D  </br><?php echo $semana_actual[7];?></b></th>
                                    <th width="5%"><b>Total</br>Semanal</b></th>
                                    <th  width="5%"> </th>
                                    <th  width="5%"> </th>
                                </tr>
                                </thead>
                                <tbody>
                                 <tr  id="semanaUno0">
                                 <td  style="display: none;"><input type="text" name="semanaUno[0][ingreso_horas_id]"  class="form-control"  id="ingreso_horas_id0" value="0" />	</td>
                                 <td>
                                     <select id="centro_contable_id0"  name="semanaUno[0][centro_contable_id]" class="form-control"  >
                                    		<option value="">Seleccione</option>
 								                <?php
 								                
								                if(!empty($listas['centro_contables']))
								                {
									                foreach ($listas['centro_contables'] AS $centro)
									                {
									               		echo '<option value="'. $centro->id .'">'. $centro->nombre .'</option>';
									                }
								                }
								                ?>
							                </select></td>
                                    <td>
                                     <select id="base_id0"  name="semanaUno[0][base_id]" class="form-control base"  data-rule-required="true" aria-required="true">
                                    <option value="">Seleccione</option>
 								                <?php
 								                
								                if(!empty($listas['base']))
								                {
								                	
									                foreach ($listas['base'] AS $base)
									                {
									               		echo '<option value="'. $base['id'] .'">'. $base['nombre'] .'</option>';
									                }
								                }
								                ?>
							                </select></td>
                                    <td><select id="actividad_id0"  name="semanaUno[0][actividad_id]" class="form-control actividad"  data-rule-required="true" aria-required="true"   >
								                <option value="">Seleccione</option>
								                <?php
 								                
								                if(!empty($listas['actividad']))
								                {
									                foreach ($listas['actividad'] AS $actividad)
									                {
									               		echo '<option value="'. $actividad['id'] .'">'. $actividad['nombre'] .'</option>';
									                }
								                }?>
							                </select></td>
                                    <td><select id="beneficio_id0" name="semanaUno[0][beneficio_id]"  class="form-control">
								                <option value="">Seleccione</option>
								                <?php
 								                
								                if(!empty($listas['beneficio']))
								                {
									                foreach ($listas['beneficio'] AS $beneficio)
									                {
									               		echo '<option value="'. $beneficio['id'] .'">'. $beneficio['nombre'] .'</option>';
									                }
								                }?>
							                </select></td>
                                    <td class="text-navy"> <select id="cuenta_gasto_id0"  name="semanaUno[0][cuenta_gasto_id]"  class="form-control beneficio" data-rule-required="true" aria-required="true"  >
								                <option value="">Seleccione</option>
								                <?php
 								                
								                if(!empty($gastos))
								                {
									                foreach ($gastos AS $gasto)
									                {
									               		echo '<option value="'. $gasto->id .'">'. $gasto->nombre .'</option>';
									                }
								                }
								                ?>
							                </select></td>
                                    <td class="text-navy"><input type="text"  maxlength="4"  data-fecha="<?php echo $dias_semana_actual[1];?>" id="lunes0" name="semanaUno[0][lunes]"  class="form-control  lunes"   ></td>
                                    <td class="text-navy"> <input type="text" maxlength="4"  data-fecha="<?php echo $dias_semana_actual[2];?>" id="martes0" name="semanaUno[0][martes]" class="form-control  martes"  ></td>
                                    <td class="text-navy"> <input type="text" maxlength="4"  data-fecha="<?php echo $dias_semana_actual[3];?>" id="miercoles0" name="semanaUno[0][miercoles]" class="form-control  miercoles"  ></td>
                                    <td class="text-navy"><input type="text"  maxlength="4"  data-fecha="<?php echo $dias_semana_actual[4];?>" id="jueves0" name="semanaUno[0][jueves]" class="form-control  jueves"  > </td>
                                    <td class="text-navy"> <input type="text" maxlength="4"  data-fecha="<?php echo $dias_semana_actual[5];?>" id="viernes0" name="semanaUno[0][viernes]" class="form-control  viernes"  ></td>
                                    <td class="text-navy"> <input type="text" maxlength="4"  data-fecha="<?php echo $dias_semana_actual[6];?>" id="sabado0" name="semanaUno[0][sabado]" class="form-control  sabado"  ></td>
                                    <td class="text-navy"><input type="text"  maxlength="4"  data-fecha="<?php echo $dias_semana_actual[7];?>" id="domingo0" name="semanaUno[0][domingo]" class="form-control  domingo"  ></td>
                                    <td class="text-navy"> <input type="text" id="filatotal0" name="semanaUno[0][semanal]" readonly="readonly" class="form-control semanal" value="0"  ></td>
                                    
                                    <td class="text-navy"> 
                                    	<button type="button" class="btn btn-default btn-block eliminarDependientesBtn" agrupador="semanaUno">
                                    		<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button></td>
                                    <td class="text-navy"> <button type="button" class="btn btn-default btn-block agregarSemana1" agrupador="semanaUno">
                                    		<i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button>
                                    	
                                    	
                                    		</td>
                                </tr>
                                 </tbody>
                                 
                                  <tfoot>
                                <tr style="font-weight: bold; ">
                                <td  style="display: none;">&#32;</td>
                                	<td>&#32;</td>
                                    <td>&#32;</td>
                                    <td>&#32;</td>
                                    <td>&#32;</td>
                                    <td><b>Total</b></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_lunes">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_martes">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_miercoles">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_jueves">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_viernes">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_sabado">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_domingo">0</span></td>
                                    <td> <span class="form-control-static" id="semanaUnoTable_semanal">0</span></td>
                                </tr>
                                 </tfoot>
                                 
                            </table>
                                        


                                        
                                    </div>
							        	 <!-- Aqui Finaliza la primera tabla -->
							        	 
							        	 <!-- Aqui empieza la segunda tabla -->
							        	 
							        	 <div class="ibox border-bottom">
                                				<div class="ibox-title"  style="background-color: #0070BA; color:#ffffff;">
                                    				<h5>Semana <?php echo (int)date("W")+1;?></h5>
                                    				<div class="ibox-tools">
                                        				Colaborador:  <span id="colaborador_nombre2"> </span>  
                                    				</div>
                                				</div>
                                			</div>
                                			<div class="table-responsive" style="overflow-y: auto;height:300px;overflow:scroll;">
										 <table id="semanaDosTable"  class="table table-noline tabla-dinamica">
                                <thead>
                                 
                                <tr>
                                    <th style="text-align: center; display: none;" width="12%"    ><b>ID</b></th>
                                    <th style="text-align: center;"  width="12%"><b>Centro Contable</b></th>
                                    <th style="text-align: center;"  width="12%"><b>Base</b><span required="" aria-required="true">*</span></th>
                                    <th style="text-align: center;" width="12%"><b>Actividad</b><span required="" aria-required="true">*</span></th>
                                    <th style="text-align: center;" width="12%"><b>Beneficio</b></th>
                                    <th style="text-align: center;" width="14%"><b>Cuenta de Gasto</b><span required="" aria-required="true">*</span></th>
                                    <th style="text-align: center;" width="5%"><b>L </br> <?php echo $semana_proxima[1];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>M  </br><?php echo $semana_proxima[2];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>Mi </br><?php echo $semana_proxima[3];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>J  </br><?php echo $semana_proxima[4];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>V  </br><?php echo $semana_proxima[5];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>S  </br><?php echo $semana_proxima[6];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>D  </br><?php echo $semana_proxima[7];?></b></th>
                                    <th style="text-align: center;" width="5%"><b>Total</br>Semanal</b></th>
                                    <th> </th>
                                    <th> </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr  id="semanaDos0">
                                	<td  style="display: none;"><input type="text" name="semanaDos[0][ingreso_horas_id]"   class="form-control"    id="ingreso_horas_id0"  value="0"/>	</td>
                                	
                                	<td>
                                     		<select id="centro_contable_id0"  name="semanaDos[0][centro_contable_id]" class="form-control">
                                    		<option value="">Seleccione</option>
 								                <?php
 								                
								                if(!empty($listas['centro_contables']))
								                {
									                foreach ($listas['centro_contables'] AS $centro)
									                {
									               		echo '<option value="'. $centro->id .'">'. $centro->nombre .'</option>';
									                }
								                }
								                ?>
							                </select>
							           </td>
                                   
                                    <td>
                                    
                                    <select id="base_id0"  name="semanaDos[0][base_id]" class="form-control"  data-rule-required="true" aria-required="true">
                                    <option value="">Seleccione</option>
 								                <?php
 								                
								                if(!empty($listas['base']))
								                {
									                foreach ($listas['base'] AS $base)
									                {
									               		echo '<option value="'. $base['id'] .'">'. $base['nombre'] .'</option>';
									                }
								                }
								                ?>
							                </select></td>
                                    <td><select id="actividad_id0"  name="semanaDos[0][actividad_id]" class="form-control"  data-rule-required="true" aria-required="true" >
								                <option value="">Seleccione</option>
								                <?php
 								                
								                if(!empty($listas['actividad']))
								                {
									                foreach ($listas['actividad'] AS $actividad)
									                {
									               		echo '<option value="'. $actividad['id'] .'">'. $actividad['nombre'] .'</option>';
									                }
								                }?>
							                </select></td>
                                    <td><select id="beneficio_id0" name="semanaDos[0][beneficio_id]"  class="form-control">
								                <option value="">Seleccione</option>
								                <?php
 								                
								                if(!empty($listas['beneficio']))
								                {
									                foreach ($listas['beneficio'] AS $beneficio)
									                {
									               		echo '<option value="'. $beneficio['id'] .'">'. $beneficio['nombre'] .'</option>';
									                }
								                }?>
							                </select></td>
                                    <td class="text-navy"> <select id="cuenta_gasto_id0"  name="semanaDos[0][cuenta_gasto_id]"  class="form-control"  data-rule-required="true" aria-required="true" >
								                <option value="">Seleccione</option>
								                <?php
 								                
								                if(!empty($gastos))
								                {
									                foreach ($gastos AS $gasto)
									                {
									               		echo '<option value="'. $gasto->id .'">'. $gasto->nombre .'</option>';
									                }
								                }
								                ?>
							                </select></td>
							        
							        <td class="text-navy"><input type="text"   maxlength="4"   data-fecha="<?php echo $dias_semana_proxima[1];?>" id="lunes0" name="semanaDos[0][lunes]"  class="form-control  lunes"  ></td>
                                    <td class="text-navy"> <input type="text" maxlength="4"    data-fecha="<?php echo $dias_semana_proxima[2];?>"  id="martes0" name="semanaDos[0][martes]" class="form-control  martes"  ></td>
                                    <td class="text-navy"> <input type="text"  maxlength="4"   data-fecha="<?php echo $dias_semana_proxima[3];?>"  id="miercoles0" name="semanaDos[0][miercoles]" class="form-control  miercoles"  ></td>
                                    <td class="text-navy"><input type="text"  maxlength="4"    data-fecha="<?php echo $dias_semana_proxima[4];?>"  id="jueves0" name="semanaDos[0][jueves]" class="form-control  jueves"  > </td>
                                    <td class="text-navy"> <input type="text" maxlength="4"    data-fecha="<?php echo $dias_semana_proxima[5];?>"  id="viernes0" name="semanaDos[0][viernes]" class="form-control  viernes"  ></td>
                                    <td class="text-navy"> <input type="text"  maxlength="4"   data-fecha="<?php echo $dias_semana_proxima[6];?>"   id="sabado0" name="semanaDos[0][sabado]" class="form-control  sabado"  ></td>
                                    <td class="text-navy"><input type="text"  maxlength="4"    data-fecha="<?php echo $dias_semana_proxima[7];?>"  id="domingo0" name="semanaDos[0][domingo]" class="form-control  domingo"  ></td>
                                    <td class="text-navy"> <input type="text" id="filatotal0" name="semanaDos[0][semanal]" readonly="readonly" class="form-control semanal" value="0"  ></td>
                     
                                    <td class="text-navy"> 
                                    	<button type="button" class="btn btn-default btn-block eliminarDependientesBtn" agrupador="semanaDos">
                                    		<i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button></td>
                                    <td class="text-navy"> <button type="button" class="btn btn-default btn-block agregarSemana2" agrupador="semanaDos">
                                    		<i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button>
                                    		
                                    		</td>
                                </tr>
                                 </tbody>
                                 
                                  <tfoot>
                                <tr  style="font-weight: bold; ">
                                <td  style="display: none;">&#32;</td>
                                 <td>&#32;</td>
                                    <td>&#32;</td>
                                    <td>&#32;</td>
                                    <td>&#32;</td>
                                   <td><b>Total</b></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_lunes">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_martes">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_miercoles">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_jueves">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_viernes">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_sabado">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_domingo">0</span></td>
                                    <td> <span class="form-control-static" id="semanaDosTable_semanal">0</span></td>
                                </tr>
                                 </tfoot>
                                 
                            </table>
                                        

                                        
                                    </div>
							        	 <!-- Aqui Finaliza la segunda tabla -->
							        	 
							        	     <div class="row">
                                          <div class="form-group form-group col-xs-12 col-sm-12  col-md-3 col-lg-3"> 
                                            &nbsp;
                                        </div>
                                          <div class="form-group form-group col-xs-12 col-sm-12  col-md-3 col-lg-3"> 
                                            <input type="button" id="verComentariosBtn" class="btn btn-primary btn-block" value="Ver Comentarios" />
                                        </div>
                                        
                                       <div class="form-group form-group col-xs-12 col-sm-12  col-md-3 col-lg-3"> 
                                            <input type="button" id="validarBtn" class="btn btn-primary btn-block" value="Listo para validar" />
                                        </div>
                                           <div class="form-group form-group col-xs-12 col-sm-12  col-md-3 col-lg-3"> 
                                             <input type="button" id="guardarBtnEntry" class="btn btn-primary btn-block" value="Guardar" />
                                           
                                        </div>
                                    </div>
							        	    


							        	 </div>
										 
<?php echo form_close(); ?>


										   
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
 <div class="modal fade" id="pantallaComentarios" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Comentarios de ausencias</h4>
            </div>
             <div class="modal-body">
                    <?php echo modules::run('planilla/ocultotablacomentarios');?> 
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
