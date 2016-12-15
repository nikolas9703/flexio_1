<style>
 .descuenta_dic{float:right;}    
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
	            <div ng-controller="toastController"></div>
                    
	            	<div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">
						<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
		            		<label>Colaborador</label>
						</div>
			        	<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
				        	<select id="colaborador_id" class="form-control white-bg chosen-select">
								<option value="">Seleccione</option>
								<?php
									foreach($colaborador_list AS $row){
										if(empty($row['id'])){
											continue;
										}

										$selected = !empty($colaborador_id_selected) && $colaborador_id_selected == $row['id'] ? 'selected="selected"' : "";
										echo '<option value="'. $row['id'] .'" '.$selected .'>'. $row['nombre'] . " " . $row['apellido'] . " " . $row['cedula'] .'</option>';
									}
								?>
							</select>
						</div>
						<div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>
					</div>
                   
                    <div class="row" style="margin-right:0px!important;">
                        <div class="ibox-title border-bottom">
							<h5>Datos del descuento</h5>
							
						</div> 
					<?php 
					$info = !empty($info) ? array("info" => $info) : array();
					
					echo modules::run("descuentos/formulario_descuento", $info);
					?>
                </div>
				<br/><br/>
				<?php
				if(empty($info)) {
				echo modules::run('descuentos/ocultoformulariocomentarios') ;
				}
				?>

        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<div class="alert alert-warning" ng-if="notificacion.campos.length || notificacion.limiteCapacidadAlcanzado.length">
	    <div ng-if="notificacion.campos.length">
		    <b>No es posible crear un nuevo descuento para este colaborador.</b><br> <b>Los siguientes datos deben ser completados en detalle del colaborador:</b>
		    <ul>
		    	<li ng-repeat="notificacion in notificacion.campos track by $index" ng-bind-html="notificacion"></li>
		    </ul>
	    </div>
	  <div ng-if="notificacion.limiteCapacidadAlcanzado.length" ng-bind-html="notificacion.limiteCapacidadAlcanzado">
	</div>
</div>
