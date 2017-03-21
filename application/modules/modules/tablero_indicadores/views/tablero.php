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
                $formAttr = array(
	                'method'       => 'POST',
	                'id'           => 'filtrarIndicadoresForm',
	                'autocomplete' => 'off',
					/*'class'		   => 'form-inline'*/
                );

                echo form_open(base_url(uri_string()), $formAttr);
                ?>
	            <!-- BUSCADOR -->
				<div class="ibox border-bottom">
					<div class="ibox-title">
						<h5>Filtrar</h5>
				        <div class="ibox-tools">
				         	<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
				    	</div>
					</div>
					<div class="ibox-content" style="display:none;">
						<!-- Inicia campos de Busqueda -->
					     	<div class="row">
					        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
					            	<label for="">Fecha</label>
									<input type="text" id="fecha_filtro" value="" class="form-control rango-fecha" readonly="readonly" />
								</div>

								<?php if(!empty($listado_subordinados)): ?>
								<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
					            	<label for="">Usuario</label>
									<select id="uuid_usuario" class="form-control">
		                           		<option value="">Seleccione</option>
		                                <?php
		                                if(!empty($listado_subordinados))
		                                {
		                                 	foreach ($listado_subordinados AS $subordinado)
		                                    {
		                                    	echo '<option value="'. $subordinado['uuid_usuario'] .'">'. $subordinado['nombre'] .'</option>';
		                                    }
		                                }
		                                ?>
		                         	</select>
								</div>
								<?php endif; ?>

								<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
									<!--<input type="button" id="searchOportunidadBtn" class="btn btn-default btn-block" value="Filtrar" />-->
									<label for="">&nbsp;</label>
									<button type="button" id="filtrarIndicadoresBtn" class="btn btn-block btn-default">Filtrar</button>
								</div>
								<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
									<!--<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />-->
									<label for="">&nbsp;</label>
									<button type="button" id="limpiarIndicadoresBtn" class="btn btn-block btn-default">Limpiar</button>
								</div>
							</div>
						<!-- Termina campos de Busqueda -->
					</div>
				</div>
				<!-- /BUSCADOR -->
	           	<?php echo form_close(); ?>

	          <!--  <div class="row">
		            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
		                <div class="widget-custom lazur-bg oportunidades-ganadas-wrapper">
		                    <!-- Oportunidades Ganadas -->
		                  <!--  <div class="row">
		                        <div class="widget-text-title text-center col-xs-12">
		                           &#931; Oportunidades Ganadas
		                        </div>
		                        <div class="widget-text-content col-xs-12 text-center">
		                            <div class="row">
		                            	<div class="col-xs-6">
		                            		<span>Venta</span>
		                            		<h3 class="font-bold no-margins venta">$<?php echo !empty($monto_total_ganado_venta[0]["monto_total"]) ? $monto_total_ganado_venta[0]["monto_total"] : "0.00";  ?></h3>
		                            	</div>
		                            	<div class="col-xs-6">
		                            		<span>Alquiler</span>
		                            		<h3 class="font-bold no-margins alquiler">$<?php echo !empty($monto_total_ganado_alquiler[0]["monto_total"]) ? $monto_total_ganado_alquiler[0]["monto_total"] : "0.00";  ?></h3>
		                            	</div>
		                            </div>
		                        </div>
		                    </div>
		                    <!-- /Oportunidades Ganadas -->
		              <!--  </div>
		            </div>
		            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
		                <div class="widget-custom navy-bg comisiones-ganadas-wrapper">
		                    <div class="row">
		                        <div class="widget-text-title text-center col-xs-12">
		                           &#931; Comisiones Ganadas
		                        </div>
		                        <div class="widget-text-content col-xs-12 text-center">
		                            <div class="row">
		                            	<div class="col-xs-6">
		                            		<span>Venta</span>
		                            		<h3 class="font-bold no-margins venta">$<?php echo !empty($monto_total_comision_venta) ? $monto_total_comision_venta[0]["monto_total_comision"] : "0.00";  ?></h3>
		                            	</div>
		                            	<div class="col-xs-6">
		                            		<span>Alquiler</span>
		                            		<h3 class="font-bold no-margins alquiler">$<?php echo !empty($monto_total_comision_alquiler) ? $monto_total_comision_alquiler[0]["monto_total_comision"] : "0.00";  ?></h3>
		                            	</div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
		                <div class="widget-custom celeste-bg oportunidades-abiertas-wrapper">
		                    <div class="row">
		                        <div class="widget-text-title text-center col-xs-12">
		                           &#931; Oportunidades Abiertas
		                        </div>
		                        <div class="widget-text-content col-xs-12 text-center">
		                            <div class="row">
		                            	<div class="col-xs-6">
		                            		<span>Venta</span>
		                            		<h3 class="font-bold no-margins venta">$<?php echo !empty($monto_total_abierto_venta) ? $monto_total_abierto_venta[0]["monto_total"] : "0.00";  ?></h3>
		                            	</div>
		                            	<div class="col-xs-6">
		                            		<span>Alquiler</span>
		                            		<h3 class="font-bold no-margins alquiler">$<?php echo !empty($monto_total_abierto_alquiler) ? $monto_total_abierto_alquiler[0]["monto_total"] : "0.00";  ?></h3>
		                            	</div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
		                <div class="widget-custom azul-oscuro-bg cantidad-propiedades-wrapper">
		                    <div class="row">
		                        <div class="widget-text-title text-center col-xs-12">
		                            &#931; Cantidad de Propiedades
		                        </div>
		                        <div class="widget-text-content col-xs-12 text-center">
		                            <div class="row">
		                            	<div class="col-xs-6">
		                            		<span>Venta</span>
		                            		<h3 class="font-bold no-margins venta"><?php echo !empty($cantidad_propiedad_disponible_venta) ? $cantidad_propiedad_disponible_venta[0]["total_disponible"] : "0.00";  ?></h3>
		                            	</div>
		                            	<div class="col-xs-6">
		                            		<span>Alquiler</span>
		                            		<h3 class="font-bold no-margins alquiler"><?php echo !empty($cantidad_propiedad_disponible_alquiler) ? $cantidad_propiedad_disponible_alquiler[0]["total_disponible"] : "0.00";  ?></h3>
		                            	</div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>-->

		        <div class="row">
		        	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

		        		<!-- Notificaciones Reciente -->
		        		<div class="ibox float-e-margins notificaciones-recientes-wrapper">
		                    <div class="ibox-title">
		                        <h5>Notificaciones Recientes</h5>
		                        <span class="label label-warning label-conteo-notificacion"><?php echo !empty($notificaciones) ? count($notificaciones) : 0; ?> Notificaciones</span>
		                        <div class="ibox-tools">
	                            	<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
	                            </div>
		                    </div>
		                    <div class="ibox-content">

 								<h4 class="text-center p-m hide"><i class="fa fa-exclamation-cirle"></i> No hay datos que mostrar</h4>

	                            <?php if(!empty($notificaciones)): ?>
	                            <ul class="lista-notificaciones-recientes list-group clear-list m-t">
	                            	<?php
	                            	$j=0;
	                            	foreach($notificaciones AS $notificacion)
									{
	                            		if(Util::is_array_empty($notificacion)){
	                            			continue;
	                            		}

										$tiempo_transcurrido = !empty($notificacion["tiempo_transcurrido"]) ? $notificacion["tiempo_transcurrido"] : "";
	                            		$mensaje = !empty($notificacion["mensaje"]) ? $notificacion["mensaje"] : "";
	                            		$fecha = !empty($notificacion["fecha"]) ? $notificacion["fecha"] : "";

										$first_item_class = $j==0 ? 'fist-item' : '';

	                            		echo '<li class="list-group-item '.$first_item_class.'">
											<span class="label label-info pull-right">'. $tiempo_transcurrido .'</span>
											'. $mensaje .'<br>
											<small>'. $fecha .'</small>
										</li>';

										$j++;
	                            	}
	                            	?>
								</ul>
								<button id="cargarNotificacionesBtn" data-limit="<?php echo !empty($notificaciones) ? count($notificaciones) : 0; ?>" class="btn btn-primary btn-block m-t"><i class="fa fa-arrow-down"></i> Ver Mas</button>
								<?php else: ?>

									<h4 class="text-center p-m"><i class="fa fa-exclamation-cirle"></i> No hay datos que mostrar</h4>

								<?php endif; ?>

		                    </div>
		                </div>
		        		<!-- /Notificaciones Reciente -->
		        	</div>
		        	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

		        		<!-- Actividades Pendientes -->
		        		<div class="ibox float-e-margins actividades-pendientes-wrapper">
		                    <div class="ibox-title">
		                        <span class="label label-warning pull-right label-conteo-actividades-pendientes"><?php echo !empty($actividades_pendientes) ? count($actividades_pendientes) : 0; ?> Actividad Pendiente</span>
		                        <h5>Actividades Pendientes</h5>
		                    </div>
		                    <div class="ibox-content">

								<h4 class="text-center p-m hide"><i class="fa fa-exclamation-cirle"></i> No hay datos que mostrar</h4>

								<?php if(!empty($actividades_pendientes)): ?>
								<ul class="lista-actividades-pendientes todo-list small-list">
									<?php
	                            	$j=0;
	                            	foreach($actividades_pendientes AS $actividad)
									{
	                            		if(Util::is_array_empty($actividad)){
	                            			continue;
	                            		}

										$uuid_actividad = !empty($actividad["uuid_actividad"]) ? $actividad["uuid_actividad"] : "";
	                            		$asunto_actividad = !empty($actividad["asunto"]) ? $actividad["asunto"] : "";
	                            		$icono_actividad = !empty($actividad["icono"]) ? $actividad["icono"] : "";
	                            		$fecha = !empty($actividad["fecha_creacion"]) ? $actividad["fecha_creacion"] : "";
	                            		$tiempo_transcurrido = !empty($fecha) ? Util::timeago($fecha) : "";
	                            		$uuid_usuario_asignado = !empty($actividad["uuid_asignado"]) ? $actividad["uuid_asignado"] : "";

	                            		echo '<li data-id-actividad="'. $uuid_actividad .'" data-asignado="'. $uuid_usuario_asignado .'">
											<a class="check-link" href="#"><i class="fa fa-square-o"></i></a>
	            							<span class="label label-info pull-right">'. $tiempo_transcurrido .'</span>
											<span class="m-l-xs"><i class="fa '. $icono_actividad .'"></i> '. $asunto_actividad .'</span>
										</li>';

										$j++;
	                            	}
	                            	?>
								</ul>

		                        <button id="cargarActividadesPendienteBtn" data-limit="<?php echo !empty($actividades_pendientes) ? count($actividades_pendientes) : 0; ?>" class="btn btn-primary btn-block m-t"><i class="fa fa-arrow-down"></i> Ver Mas</button>
		                        <?php endif; ?>

		                    </div>
		                </div>
		        		<!-- /Actividades Pendientes -->
		        	</div>
		        </div>

		        <div class="row">
		        	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		        		<!-- Pipeline de Ventas -->
		        		<div class="ibox float-e-margins">
	                        <div class="ibox-title">
	                            <h5>Pipeline de Ventas</h5>
	                        </div>
	                        <div class="ibox-content">

	                       		<!-- Grafica -->
	                        	<div id="containerChart" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto"></div>
	                        	<!-- /Grafica -->

	                       	</div>
	                    </div>
		        		<!-- Pipeline de Ventas -->
		        	</div>
		        	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		        		<!-- Clientes score -->
		        		<div class="ibox float-e-margins">
	                        <div class="ibox-title">
	                            <h5>Client Score Average</h5>
	                        </div>
	                        <div class="ibox-content">

	                       		<!-- Grafica -->
	                       		<div id="container-clientes-score" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
	                       		<!-- /Grafica -->

	                       	</div>
	                    </div>
		        		<!-- /Clientes score -->
		        	</div>
		        </div>

		        <div class="row">
		        	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		        		<!-- Top Oportunidades -->
		        		<div class="ibox float-e-margins">
	                        <div class="ibox-title">
	                            <h5>Top 20 Oportunidades</h5>
	                        </div>
	                        <div class="ibox-content">
	                             <div id="top-oportunidades" style="min-width: 610px; height: 400px; margin: 0 auto"></div>
	                        </div>
	                    </div>
		        		<!-- Top Oportunidades -->
		        	</div>
		        </div>
            <div class="row">

      				<div class="NoRecords text-center lead"></div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		        		<!-- Top Oportunidades -->
		        		<div class="ibox float-e-margins">
	                        <div class="ibox-title">
	                            <h5>Lista Usuario Actividades</h5>
	                        </div>
    	              <div class="ibox-content">
          				<!-- the grid table -->
          				       <table class="table table-striped" id="usuariosGrid"></table>

          				<!-- pager definition -->
          				      <div id="pager"></div>
                   </div>
                </div>
      			</div>
        	</div>

    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
</div><!-- cierra #wrapper -->