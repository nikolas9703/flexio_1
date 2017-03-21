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

        		<div class="tabs-container">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#cargos" data-toggle="tab" aria-expanded="false"> Cargos</a></li>
	          <li><a href="#departamentos" data-toggle="tab" aria-expanded="true">&Aacute;rea de Negocio</a></li>
						<li class="hide"><a href="#tiempocontratacion" data-toggle="tab" aria-expanded="true">Tiempo de Contrataci&oacute;n</a></li>
            <li><a href="#liquidaciones" data-toggle="tab" aria-expanded="true">Liquidaciones</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="cargos" ng-cloak="" ng-controller="configCargosCtrl"> <!-- SECION O TAB DE CARGOS -->
							<div class="panel-body">
								<!-- Tabs: Cargos -->
								<?php
		                        $formAttr = array(
		                            'method'        => 'POST',
									'name'			=> 'crearCargoForm',
		                            'id'            => 'crearCargoForm',
		                            'autocomplete'  => 'off'
		                        );
		                        echo form_open(base_url(uri_string()), $formAttr);
		                        ?>
		                         <div class="row"><p class="text-danger pull-right m-r">Todos los campos son obligatorios</p></div>
		                         <div class="row">
		                         	<!--<div class="col-sm-2">
		                         		<div class="form-group">
		                         			<label>&Aacute;rea de Negocio</label>
		                         			<select name="departamento_id" id="departamento_id" class="form-control chosen-field" data-rule-required="true" data-msg-required="" ng-model="cargo.departamento_id">
			                                	<option value="">Seleccione</option>
			                                	<option ng-repeat="departamento in departamentosList1 track by $index" value="{{departamento.id && '' || departamento.id}}" ng-show="departamento.estado == 1">{{departamento.nombre && '' || departamento.nombre}}</option>
			                                </select>
		                         		</div>
		                         	</div>-->
		                         	<div class="col-sm-2">
		                         		<div class="form-group">
		                         			<label>Cargo</label>
		                         			<input type="text" name="nombre" id="nombre" class="form-control" data-rule-required="true" data-msg-required="" ng-model="cargo.nombre" />
		                         		</div>
		                         	</div>
		                         	<div class="col-sm-3">
		                         		<div class="form-group">
		                         			<label>Descripcion</label>
		                         			<input type="text" name="descripcion" id="descripcion" class="form-control" data-rule-required="true" data-msg-required="" ng-model="cargo.descripcion" />
		                         		</div>
		                         	</div>
		                         	<div class="col-sm-2">
		                         		<div class="form-group">
		                         			<label>Tipo de Rata</label>
		                         			<select name="tipo_rata" id="tipo_rata" class="form-control" data-rule-required="true" data-msg-required="" ng-model="cargo.tipo_rata">
			                                	<option value="">Seleccione</option>
			                                	<option ng-repeat="tipo in tipo_ratas track by $index" value="{{tipo && '' || tipo}}">{{tipo && '' || tipo}}</option>
			                                </select>
		                         		</div>
		                         	</div>
		                         	<div class="col-sm-2">
		                         		<div class="form-group">
		                         			<label>Rata</label>
			                         		<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="text" name="rata" id="rata" class="form-control" data-rule-required="true" data-msg-required="" ng-model="cargo.rata" />
											</div>
										</div>
		                         	</div>
		                         	<div class="col-sm-1">
		                         		<div class="form-group">
		                         			<label>Codigo</label>
		                         			<input type="text" name="codigo" class="form-control" data-rule-required="true" data-msg-required="" disabled="disabled" />
		                         		</div>
		                         	</div>
		                         </div>
		                         <div class="row">
		                         	<div class="col-sm-8"></div>
		                         	<div class="col-sm-2"><input type="button" id="cancelarFormBtn" class="btn btn-w-m btn-default pull-right" value="Cancelar" ng-click="cancelar($event)" /></div>
		                         	<div class="col-sm-2">
		                         		<button class="btn btn-w-m btn-success" id="guardarCargoBtn" name="guardarCargoBtn" type="button" ng-model="cargo.guardarcargoBtn" ng-click="guardar($event)">Guardar</button>
		                         	</div>
		                         </div>
		                        <input type="hidden" name="id" ng-model="cargo.id" value="" />
		                        <?php echo form_close(); ?>

							</div>
              <?php echo modules::run('configuracion_rrhh/ocultotablacargos'); ?>
						</div> <!-- FIN TAB DE CARGOS -->

            <!-- INICIO TAB DE AREA DE NEGOCIO -->
						<div class="tab-pane" id="departamentos" ng-cloak="" ng-controller="configDepartamentosCtrl">
							<div class="panel-body">
								<!-- Tabs: Departamentos -->
								<?php
		                        $formAttr = array(
		                            'method'        => 'POST',
		                            'id'            => 'departamentoForm',
		                            'autocomplete'  => 'off',
									//'class'			=> 'form-inline'
		                          );
		                         echo form_open(base_url(uri_string()), $formAttr);
		                        ?>
                                <div class="row">
                                	<div class="col-xs-12 col-sm-4">
                                		<div class="form-group">
                                			<label>&Aacute;rea de Negocio <span class="required">*</span></label>
                                			<input type="text" name="nombre" id="nombreAreaNeg" value="" class="form-control" placeholder="" ng-model="nombre_departamento" data-rule-required="true" data-msg-required="" />
                                		</div>
                                		<div class="form-group">
	                                        <a href="#" id="guardarDepartamentoBtn" name="guardarDepartamentoBtn" class="btn btn-w-m btn-primary" ng-click="guardar($event)">Agregar</a>
	                                    </div>
                                	</div>
                                	<div class="col-xs-12 col-sm-8 b-l">

										<div class="row hide"> //***************** ESTE DIV ESTA ESCONDIDO **************
											<div class="col-sm-12">

		                                		<div class="form-group">
		                                			<label>Listado de &Aacute;rea de Negocio</label>

													<table id="dtable" class="table table-bordered">
														<thead>
															<tr>
																<th></th>
																<th>Nombre</th>
																<th>Estado</th>
																<th>Centro Contable</th>
															</tr>
														</thead>
														<tbody>
															<tr ng-repeat="departamento in departamentosList2 track by $index">
																<td width="5%" align="center"><input type="checkbox" ng-model="selected[departamento.id && '' || departamento.id]" name="departamento[]" value="{{departamento.id && '' || departamento.id}}" /></td>
																<td>{{departamento.nombre && '' || departamento.nombre}}</td>
																<td>{{departamento.estado && '' || departamento.estado}}</td>
																<td>{{departamento.centro && '' || departamento.centro}}</td>
															</tr>
														</tbody>
													</table>
		                                		</div>

		                                	</div>
		                                </div>

		                    <div class="row">
											<div class="col-sm-6 hide"> //***************** ESTE DIV ESTA ESCONDIDO **************

		                                		<div class="form-group">
			                                        <label>Centro Contable</label>
			                                        <div class="input-group">
			                                			<select name="centro_contable_id" id="centro_contable_id" class="form-control chosen-centro" multiple="multiple" data-placeholder="Seleccione" ng-model="centro_contables">
						                                	<option value="">Seleccione</option>
						                                	<option ng-repeat="centro_contable in centroContableList track by $index" value="{{centro_contable.id && '' || centro_contable.id}}">{{centro_contable.nombre && '' || centro_contable.nombre}}</option>
						                                </select>
						                                <span class="input-group-btn">
											            	<button type="button" class="btn btn-primary" ng-click="relacionarCentro($event)">Enviar</button>
											            	<button type="button" class="btn btn-default" ng-click="cancelarCentro($event)">Cancelar</button>
											          	</span>
										          	</div>
                              </div>
                    </div>
		                                    <div class="col-sm-6">

			                                    <div class="form-group">
			                                        <label>Estado</label>
			                                        <div class="input-group">
			                                			<select class="form-control" id="opciones">
						                                	<option value="">Seleccione</option>
						                                	<option value="0">Desactivar</option>
						                                	<option value="1">Activar</option>
						                                </select>
						                                <span class="input-group-btn">
											            	<button type="button" class="btn btn-primary" ng-click="toggleEstado($event)">Enviar</button>
											            	<button type="button" class="btn btn-default" ng-click="cancelarEstado($event)">Cancelar</button>
											          	</span>
										          	</div>
			                                    </div>
	                                    	</div>
	                                   </div>

                                	</div>
                                </div>
                            	<?php echo form_close(); ?>
							</div>
              <?php echo modules::run('configuracion_rrhh/ocultotablaareanegocio'); ?> <!-- SE MUESTRA LA TABLA DE AREA DE NEGOCIO -->
						</div>
            <!-- FIN DE TAB AREA DE NEGOCIO -->
            <!-- INCIO DE TAB AREA DE TIEMPO CONTRATACION -->
						<div class="tab-pane hide" id="tiempocontratacion" ng-cloak="" ng-controller="configTiempoContratacionCtrl">
							<div class="panel-body">
								<!-- Tabs: Tiempo de Contratacion -->
								<?php
		                        $formAttr = array(
		                            'method'        => 'POST',
		                            'id'            => 'tiempoContratacionForm',
		                            'autocomplete'  => 'off',
		                          );
		                         echo form_open(base_url(uri_string()), $formAttr);
		                        ?>
                                <div class="row">
                                	<div class="col-xs-12 col-sm-4">
                                		<div class="form-group">
                                			<label>Tiempo <span class="required">*</span></label>
                                			<input type="text" name="tiempo_contratacion" value="" class="form-control" placeholder="" ng-model="tiempo_contratacion" data-rule-required="true" data-msg-required="" />
                                		</div>
                                		<div class="form-group">
	                                        <a href="#" id="guardarTiempoContratacionBtn" name="guardarTiempoContratacionBtn" class="btn btn-w-m btn-primary" ng-click="guardarTiempoContratacion($event)">Agregar</a>
	                                    </div>
                                	</div>
                                	<div class="col-xs-12 col-sm-8 b-l">

										<div class="row">
											<div class="col-sm-12">

		                                		<div class="form-group">

													<table id="tcntable" class="table table-bordered">
														<thead>
															<tr>
																<th>Nombre</th>
																<th></th>
															</tr>
														</thead>
														<tbody>
															<tr ng-repeat="tiempo_contratacion in lista_tiempo_contratacion track by $index">
																<td width="90%">{{tiempo_contratacion.tiempo && '' || tiempo_contratacion.tiempo}}</td>
																<td width="10%">
																	<a class="btn btn-danger" data-tiempo-id="{{tiempo_contratacion.id && '' || tiempo_contratacion.id}}" data-tiempo-valor="{{tiempo_contratacion.tiempo && '' || tiempo_contratacion.tiempo}}" ng-click="eliminarTiempoContratacion($event)"> <i class="fa fa-trash"></i></a>
																</td>
															</tr>
															<!--<tr ng-show="{{lista_tiempo_contratacion == ''}}">
																<td ng-show="{{lista_tiempo_contratacion == ''}}" colspan="2" class="text-center"><small>No se han creado Tiempos de Contrataci&oacute;n</small></td>
															</tr> -->
														</tbody>
													</table>
		                                		</div>

		                                	</div>
		                                </div>


                                	</div>
                                </div>
                            	<?php echo form_close(); ?>
							</div>
						</div>
            <div class="tab-pane" id="liquidaciones" ng-cloak="" ng-controller="configLiquidacionesCtrl">
							<div class="panel-body">
								<!-- Tabs: Tiempo de Contratacion -->
								<?php
		                        $formAttr = array(
		                            'method'        => 'POST',
		                            'id'            => 'liquidacionesForm',
		                            'autocomplete'  => 'off',
		                          );
		                         echo form_open(base_url(uri_string()), $formAttr);
		                        ?>
                                <div class="row">
                                	<div class="col-xs-12 col-sm-4">
                                		<div class="form-group">
                                			<label>Nombre <span class="required">*</span></label>
                                			<input type="text" name="item_liquidacion" value="" class="form-control" ng-model="liquidaciones.nombre" placeholder="" data-rule-required="true" />
                                		</div>
                                	</div>
                                  <div class="col-xs-12 col-sm-4">
                                		<div class="form-group">
                                			<label>Estado <span class="required">*</span></label>
                                      <select name="estado_liquidacion" ng-model="liquidaciones.estado" id="estado_id" class="form-control">
                                        <option value="">Seleccione</option>
			                                	<option ng-repeat="estado in estado_liquidaciones track by $index" value="{{estado.valor}}">{{estado.etiqueta}}</option>
                                      </select>
                                		</div>
                                	</div>
                                  <div class="col-xs-12 col-sm-12">
                                    <div class="col-xs-12 col-sm-9"> </div>
                                    <div class="col-xs-12 col-sm-3">
                                    <div class="form-group">
                                      <input type="hidden" name="id" ng-model="liquidaciones.id" value="" />
                                          <a href="#" id="guardarLiquidacionesBtn" name="guardarLiquidacionesBtn" class="btn btn-w-m btn-primary" ng-click="guardarLiquidaciones($event)">Guardar</a>
                                          <div class="col-sm-2"><input type="button" id="cancelarFormBtn" class="btn btn-w-m btn-default pull-right" value="Cancelar" ng-click="cancelarLiquidaciones($event)" /></div>

                                      </div>
                                    </div>
                                  </div>

										<div class="row">
											<div class="col-sm-12">
                        <?php echo modules::run('configuracion_rrhh/ocultotablaliquidaciones'); ?>
		                                	</div>
		                                </div>

                                </div>
                            	<?php echo form_close(); ?>
							</div>
						</div>
					</div>
				</div><!-- end tabs-container -->


                <!--<div class="ibox float-e-margins border-bottom">
                	<div class="ibox-title">
                    	<h5>Buscar</h5>
                        <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-down"></i></a></div>
	                 </div>
	                 <div class="ibox-content" style="display: none;">
	                 	<?php
                        $formAttr = array(
                            'method'        => 'POST',
                            'id'            => 'buscarCargoForm',
                            'autocomplete'  => 'off'
                          );
                         //echo form_open(base_url(uri_string()), $formAttr);
                        ?>
                        <div class="row">
				        	<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
				            	<label for="">&Aacute;rea de negocio</label>
				            	<input type="text" id="departamento" class="form-control" value="" />
							</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
				            	<label for="">Cargo</label>
				            	<input type="text" id="cargo" class="form-control" value="" />
							</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                         		<label>Rata</label>
	                         	<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="text" id="rata_valor" class="form-control" data-inputmask="'mask': '9*{1,13}[.*{1,20}]', 'greedy':false" />
								</div>
							</div>
					  		<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
								<label for="">Codigo</label>
					  			<input type="text" id="codigo" class="form-control" value="" />
							</div>
						</div>
						<div class="row">
				        	<div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
								<input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
							</div>
							<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
								<input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
							</div>
						</div>
                        <?php //echo form_close(); ?>
	                 </div>
                 </div>-->

                <!-- JQGRID -->
				<?php //echo modules::run('configuracion_rrhh/ocultotablacargos'); ?>
				<!-- /JQGRID -->

        	</div><!-- cierra .wrapper-content -->
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "opcionesModalAreaNeg",
	"size" => "sm"
))->html();

?>
