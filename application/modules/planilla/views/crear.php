<style>
a::after {
   display: none;
}

form div.form-group, form[enctype="multipart/form-data"] div.Mapa{
	height: none;
}
.ui-jqdialog {
  position: absolute !important;
  left: 50% !important;
  top: 50%!important;
  transform: translate(-50%, -50%) !important; /* Yep! */
  width: 35% !important;
  height: 12% !important;
}
.fm-button{
    height: 25px !important;
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
                  	         $formAttr = array(
			                            'method'        => 'POST',
			                            'id'            => 'crearPlanilla',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);

			                        ?>

  <div id="formulari3333o" class="panel-group">
							<div class="panel panel-white">
								<div class="panel-heading">
									<h5 class="panel-title">
										<a href="#collapseFormulario" data-parent="#collapseFormulario" data-toggle="collapse" aria-expanded="true" class="">Datos de la planilla</a>
									</h5>
								</div>
								<div class="panel-collapse collapse in" id="collapseFormOne" aria-expanded="true" style="">
									<div class="panel-body">
                    <div class="row">
  							        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
 							            	<label for="">Tipo de planilla</label>


                  							 <select id="tipo_id" name="tipo_id" class="form-control select2" <?php echo $disabled;?>>
  								                <?php
 								                if(!empty($tipo_planilla))
 								                {
 									                foreach ($tipo_planilla AS $tipo)
 									                {
 									                	$selected = '';
 									                	if(isset($info['info']['tipo']['id_cat'])){
 									                		$selected = ($tipo->id_cat ==$info['info']['tipo']['id_cat'] ? 'selected="selected"' : "");
 									                	}
  									               		echo '<option value="'. $tipo->id_cat .'" '.$selected.'>'. $tipo->etiqueta .'</option>';
 									                }
 								                }
 								                ?>
 							                </select>




 										</div>

 										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
 							            	<label for="">Rango de fechas</label>
 							            	 <div class="form-inline">
 				                                <div class="form-group">
 				                                    <div class="input-group">
 				                                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
 				                                      <input   type="text" name="rango_fecha1" id="rango_fecha1" value="<?php echo !empty($info['info']['rango_fecha1'])?date("d/m/Y", strtotime($info['info']['rango_fecha1'])):''?>" class="form-control">
 				                                      <span class="input-group-addon">a</span>
 				                                      <input  type="text" class="form-control" name="rango_fecha2"  value="<?php echo !empty($info['info']['rango_fecha2'])?date("d/m/Y", strtotime($info['info']['rango_fecha2'])):''?>"   id="rango_fecha2">
 				                                    </div>
 				                                </div>
 				                            </div>

 										</div>


 										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
 							            	<label for="">Deducciones aplicables</label>

 							            	<select id="deducciones[]"  name="deducciones[]" class="form-control select2" multiple="multiple"  data-rule-required="true">
                                          <?php
                                         if(!empty($deducciones))
                                         {
                                             foreach ($deducciones AS $deduccion) {

                                               $selected = '';
                                                if(isset($info['info']->deducciones2) && count($info['info']->deducciones2) > 0){
                                                  foreach ($info['info']->deducciones2 AS $deduccion2) {
                                                      if($deduccion2->deduccion_id == $deduccion->id){
                                                          $selected = ' selected="selected"';
                                                          continue;
                                                      }
                                                  }
                                                }
                                                echo '<option '.$selected.' value="'. $deduccion->id .'">'. $deduccion->nombre .'</option>';
                                             }
                                           }
                                         ?>
                                     </select>

 							            	</div>

 										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
 							            	<label for="">Acumulados aplicables</label>
 									<select id="acumulados[]"  name="acumulados[]" class="form-control select2" multiple="multiple" data-rule-required="true">
                                         <?php
                                         if(!empty($acumulados))
                                         {
                                              foreach ($acumulados AS $acumulado) {
                                               $selected = '';
                                                   if(isset($info['info']->acumulados2) && count($info['info']->acumulados2) > 0)
                                                   {
                                                     foreach ($info['info']->acumulados2 AS $acumulados2) {
                                                         if($acumulados2->acumulado_id == $acumulado->id){
                                                             $selected = ' selected="selected"';
                                                             continue;
                                                         }
                                                     }
                                                   }

                                                 echo '<option '.$selected.' value="'. $acumulado->id .'">'. $acumulado->nombre .'</option>';
                                             }
                                         }
                                         ?>
                                     </select>										</div>
 									</div>
									 <div class="row">

 							      <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	  <label for="">Cuenta a debitar</label> <span required="" aria-required="true">*</span>
                              <select id="cuenta_debito_id" name="cuenta_debito_id" class="form-control select2"  data-rule-required="true">
                                 <option value="">Seleccione</option>
                                 <?php
                                 if(!empty($cuentas_debito))
                                 {
                                   foreach ($cuentas_debito AS $cuenta)
                                   {
                                     $selected = '';
                                     if(isset($info['info']->cuenta_debito_id)){
                                       $selected = ($cuenta->id == $info['info']->cuenta_debito_id ? 'selected="selected"' : "");
                                     }
                                     echo '<option value="'. $cuenta->id .'" '.$selected.'>'. $cuenta->codigo.' - '.$cuenta->nombre .'</option>';
                                   }
                                 }
                                 ?>
                               </select>
										</div>

										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                      <label for="">Cuenta por pagar</label> <span required="" aria-required="true">*</span>
                         <select id="pasivo_id" name="pasivo_id" class="form-control select2" data-rule-required="true" >
                            <option value="">Seleccione</option>
                            <?php
                            if(!empty($cuentas_pasivos))
                            {
                              foreach ($cuentas_pasivos AS $cuenta)
                              {
                                $selected = '';
                                if(isset($info['info']->pasivo_id)){
                                  $selected = ($cuenta->id == $info['info']->pasivo_id? 'selected="selected"' : "");
                                }
                                echo '<option value="'. $cuenta->id .'" '.$selected.'>'. $cuenta->codigo.' - '.$cuenta->nombre .'</option>';
                              }
                            }
                            ?>
                          </select>
 										</div>


                       <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label for="">Centro Contable</label>
                                <select id="centro_contable_id" name="centro_contable_id[]"  class="form-control select2" multiple="multiple" <?php echo $disabled;?> data-rule-required="true" >
                                    <?php
                                   if(!empty($centro_contables))
                                   {
                                     foreach ($centro_contables AS $centro)
                                     {

                                       $selected ='';
                                        if(isset($info['info']->centros_contables) && count($info['info']->centros_contables) > 0)
                                             foreach ($info['info']->centros_contables AS $valor) {

                                                 if($valor->centro_contable_id == $centro->id){
                                                     $selected = ' selected="selected"';
                                                     continue;
                                                 }
                                             }
                                       echo '<option value="'. $centro->id .'" '.$selected.'>'.$centro->nombre .'</option>';
                                     }
                                   }
                                   ?>
                                 </select>
                      </div>

                       <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                <label for="">&Aacute;rea de negocio</label>
                                <select id="area_negocio_id" name="area_negocio_id" class="form-control select2"   <?php echo $disabled;?>>
                                  <option value="">Seleccione</option>
                                  <?php
                                  if(!empty($areas_negocio))
                                  {
                                    foreach ($areas_negocio AS $area)
                                    {
                                      $selected = '';
                                      if(isset($info['info']->area_negocio)){
                                        $selected = ($area->id ==$info['info']->area_negocio ? 'selected="selected"' : "");
                                      }
                                      echo '<option value="'. $area->id .'" '.$selected.'>'.$area->nombre .'</option>';
                                    }
                                  }
                                  ?>
                                </select>
                      </div>
 									</div>
									 <div class="row">

										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	<label for="">Ciclo de pago</label>
							            	<select id="ciclo_id" name="ciclo_id" class="form-control select2" <?php echo $disabled;?> data-rule-required="true">
								                <option value="">Seleccione</option>
								                <?php
								                if(!empty($ciclos))
								                {
									                foreach ($ciclos AS $ciclo)
									                {
									                	$selected = '';
									                	if(isset($info['info']['ciclo_id'])){
									                		$selected = ($ciclo->id_cat == $info['info']['ciclo_id'] ? 'selected="selected"' : "");
									                	}
 									               		echo '<option value="'. $ciclo->id_cat .'"'.$selected.'>'. $ciclo->etiqueta .'</option>';
									                }
								                }
								                ?>
							                </select>
										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">

										</div>
										<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">

										</div>
									</div>

									 <?php
                  					 if(preg_match("/ver/i", $_SERVER['REQUEST_URI']) ){ ?>

									 <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarBtnPlanilla" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarBtnPlanilla" class="btn btn-primary btn-block" value="Guardar" />
                                        </div>
                                     </div>
                                    <?php  } else{

                                    	if($tipo_planilla_creacion =='vacaciones' || $tipo_planilla_creacion =='liquidaciones' || $tipo_planilla_creacion =='licencias'){?>
                                    		 <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarBtnPlanillaNoRegular" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarBtnPlanillaNoRegular" class="btn btn-primary btn-block" value="Guardar" />
                                        </div>

                                    </div>
                                    	<?php }

                                    } ?>

									</div>
								</div>
							</div>

 						 <div id="selects"  style="display:none;">
	 						 <select id="recargos_id" name="recargos_id" class="form-control"  style="display:none;">
									                <option value="">Seleccione</option>
									                <?php
									                if(!empty($recargos))
									                {
										                foreach ($recargos AS $recargo)
										                {

	 									               		echo '<option value="'. $recargo['id'] .'">'. $recargo['nombre'] .'</option>';
										                }
									                }
									                ?>
								                </select>

							<select id="beneficios_id" name="beneficios_id" class="form-control"  style="display:none;">
									                <option value="">Seleccione</option>
									                <?php
									                if(!empty($beneficios))
									                {
										                foreach ($beneficios AS $beneficio)
										                {

	 									               		echo '<option value="'. $beneficio['id'] .'">'. $beneficio['nombre'] .'</option>';
										                }
									                }
									                ?>
							</select>
							<select id="cuenta_gasto_id" name="cuenta_gasto_id" class="form-control"  style="display:none;">
									                <option value="">Seleccione</option>
									                <?php
									                if(!empty($cuenta_gastos))
									                {
										                foreach ($cuenta_gastos AS $cuenta)
										                {

	 									               		echo '<option value="'. $cuenta->id .'">'. $cuenta->nombre .'</option>';
										                }
									                }
									                ?>
							</select>
							<select id="cuenta_costo_id" name="cuenta_costo_id" class="form-control"  style="display:none;" >
									                <option value="">Seleccione</option>
									                <?php
									                if(!empty($cuenta_costos))
									                {
										                foreach ($cuenta_costos AS $cuenta)
										                {

	 									               		echo '<option value="'. $cuenta->id .'">'. $cuenta->codigo.' - '.$cuenta->nombre .'</option>';
										                }
									                }
									                ?>
							</select>
							<select id="centro_contable_id_global" name="centro_contable_id_global" class="form-control"   style="display:none;">
	 								                <?php
									                if(!empty($centro_contable))
									                {
										                foreach ($centro_contable AS $centro)
										                {

	 									               		echo '<option value="'. $centro['id'] .'">'. $centro['nombre'] .'</option>';
										                }
									                }
									                ?>
							</select>
 						 </div>

						</div>

                 	 <?php

                   	 if(preg_match("/ver/i", $_SERVER['REQUEST_URI'])){
 						if($tipo_planilla_creacion =='vacaciones' || $tipo_planilla_creacion =='liquidaciones' || $tipo_planilla_creacion =='licencias'){

  							echo modules::run('accion_personal/ocultotabla',  isset( $lista_seleccionada) ?$lista_seleccionada:array());

						}
						else if($tipo_planilla_creacion =='xiii_mes'){
 							echo modules::run('planilla/ocultotabladecimo', isset( $info['info']) ?$info['info']:array());
						}
						else{

							 echo modules::run('planilla/ocultotablacolaborador', isset( $info['info']) ?$info['info']:array());
						}

                	  }
                	 else{

                	if($tipo_planilla_creacion == 'regular'){
                	?>
                 	<div id="accordion" class="panel-group">


							<div class="panel panel-white">
								<div class="panel-heading">
									<h5 class="panel-title">
 										Colaboradores
									</h5>
								</div>
								<div class="panel-collapse " id="collapseTwo" aria-expanded="true" style="">

 									<div class="row">
										<div class="col-xs-5">
 											<select name="from[]" id="lista_colaboradores" class="form-control" size="8" multiple="multiple">
                         <?php
                        /*if(!empty($lista_colaboradores))
                        {
                          foreach ($lista_colaboradores AS $colaborador)
                          {
                             echo '<option value="'. $colaborador->id .'">'. $colaborador->cedula." - ".$colaborador->nombre_completo.'</option>';
                          }
                        }*/
                        ?>
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
<br>
			 					<div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input  type="button"  id="cancelarBtnCol" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input  type="button"  id="guardarBtnCol" class="btn btn-primary btn-block" value="Guardar" />
                                        </div>

                                    </div>


								</div>
							</div>

 						</div>

  				<?php  }
  					else if($tipo_planilla_creacion =='vacaciones' || $tipo_planilla_creacion =='liquidaciones'|| $tipo_planilla_creacion =='licencias'){
    					  	echo modules::run('accion_personal/ocultotabla', isset( $lista_seleccionada) ?$lista_seleccionada:array());
   			 		}
				} ?>

 				 <?php echo form_close(); ?>

				<?php if(preg_match("/ver/i", $_SERVER['REQUEST_URI'])){?>
					<br/><br/>
				<?php echo modules::run('planilla/ocultoformulariocomentarios'); }?>
				</div>
                 <?php
 ?>
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



  <?php if(preg_match("/ver/i", $_SERVER['REQUEST_URI'])){
  	$tipo = isset($tipo_planilla_creacion)?$tipo_planilla_creacion:'';

  	echo Modal::config(array(
  			"id" => "pagoEspecialModal",
  			"size" => "lg",
  			"titulo" => "Validar planilla de ".$tipo,
  			"contenido" => modules::run("planilla/formulario_pagoespecial_liquidacion", array())
  	))->html();



                 	?>
<div class="modal fade" id="pantallaAgregarComentario" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><b>Comentario: </b><span id="fecha_imprimir"></span> </h4>
            </div>
           <?php
			                        $formAttr = array(
			                            'method'        => 'POST',
			                            'id'            => 'formularioComentario',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>

             <div class="modal-body">

             <input type="hidden" name="ingresohoras_dias_id"  id="ingresohoras_dias_id" value="0" />
             <input type="hidden" name="hidden_form_input"  id="hidden_form_input"   />

             <label for="">Comentario</label>
             <textarea name="comentario" id="comentario" rows="" cols=""  class="form-control"></textarea>
             </div>
            <div class="modal-footer">
                 <div class="row">
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>
	   		   </div>
	   		   <div class="form-group col-xs-12 col-sm-6 col-md-6">
	   		   		<button id="GuardarComentario" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button>
	   		   </div>
	   		   </div></div>

	   		   <?php echo form_close(); ?>
        </div>
    </div>
</div>

 <div class="modal fade" id="planillaRegularModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
         <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Cerrar planilla: <?php echo $info['info']['key'];?> </h4>
            </div>

             <?php
			                        $formAttr = array(
			                            'method'        => 'POST',
			                            'id'            => 'formularioCerrarModal',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>


             <div class="modal-body">
              		<div class="row">
						 <div class="alert alert-warning">
                          	&iexcl;Atenci&oacute;n! Esta acci&oacute;n no puede ser revertida.
                          </div>
                   </div>
                   <div class="row "  >
   							       <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
 				 						<label for="">&nbsp;</label><br>
 				 						<span>&nbsp;</span><br>
							        	<span style="margin-bottom: 20px;" class="pull-left"><h4>Planilla</h4></span><span class="pull-right label label-success"><?php echo date("d/m/Y", strtotime($info['info']['rango_fecha1']))
							        	.' - '.date("d/m/Y", strtotime($info['info']['rango_fecha2'])) ; ?></span>
 							       </div>
								   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for="">$<span id="salario_neto">0.00</span></label><br>
 							            	<span class="pull-left">Total de salario Neto</span><span class="pull-right"><span id="salario_neto_porcentaje">0</span>%</span><br>
							            	<div class="progress">
				                                <div style="aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" id="salario_neto_progress_bar" role="progressbar" class="progress-bar progress-bar-success">
				                                    <span class="sr-only">50% Complete (success)</span>
				                                </div>
				                            </div>
									</div>
 					 </div>

					 <div class="row"> <hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
  							        		<span>&nbsp;</span><br>
   							            	<label for=""><h4>$<span id="salario_bruto">0.00</span></h4></label><br>
   							            	<span class="pull-left">Total de Planilla</span><!-- <span class="pull-right">100%  </span> -->
										</div>
										<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h4>$<span id="bonificaciones">0.00</span></h4></label><br>
 							            	<span class="pull-left">Bonificaciones & comisiones</span><span class="pull-right"><span id="bonificaciones_porcentaje">0</span>%</span> <br>
							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" aria-valuemin="0"  id="bonificaciones_progress_bar"  aria-valuenow="0" role="progressbar" class="progress-bar progress-bar-success">
				                                    <span class="sr-only">35% Complete (success)</span>
				                                </div>
				                            </div>
					 				  </div>
					 </div>
					 <div class="row "     ><hr style="margin-top: 1px;margin-bottom: 1px;">
  							        	<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

  							        	 	<span>&nbsp;</span><br>
   							            	<label for=""><h4> <span id="total_colaboradores"></span></h4></label><br>

   							            	<span class="pull-left">Colaboradores</span><br>

										</div>
											<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
							            	<label for=""><h4>$<span id="descuentos">0.00</span></h4></label><br>
 							            	<span class="pull-left">Descuentos</span> <span class="pull-right"> <span id="descuentos_porcentaje">$0.00</span>%</span><br>
							            	<div class="progress">
				                                <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" id="descuentos_progress_bar" class="progress-bar progress-bar-success">

				                                </div>
				                            </div>
										</div>
 					 </div>


            </div>

              <?php echo form_close(); ?>
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
