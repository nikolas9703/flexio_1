<div role="tabpanel">
  					<!-- Tab panes -->
  					<div class="row tab-content">
  						<div role="tabpanel" class="tab-pane active" id="tabla">

  							<div class="row">
  								<div class="col-lg-12">
  									<div class="">
  										<ul class="nav nav-tabs">
  											<li class="active" id="tab_ramos"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="generales">Ramos</a></li>
                        <?php 
                        if ($accesoplan['plancrear']==1) {
                          ?>
                          <li class="" id="tab_planes"><a data-toggle="tab" href="#tab-3" aria-expanded="false"  data-targe="beneficios">Planes</a></li>
                          <?php
                        }
                        ?>  											
  										</ul>
  										<div class="tab-content">
  											<div id="tab-1" class="tab-pane active">
  												<div class="panel-body" style="padding: 0px 15px 0px 0px!important">

  													<div class="tab-content row" ng-controller="configRamosController">
  														<!-- Tab panes -->

  														<!-- BUSCADOR -->

  														<!-- Inicia campos de Busqueda -->
  														<div class="ibox-content tab-pane fade in active" id="Impuesto">
  															<div id="mensaje_info"></div>
  															<?php
  															$formAttr = array(
  																'method'        => 'POST',
  																'id'            => 'crearRamosForm',
  																'autocomplete'  => 'off'
  																);
  															echo form_open(base_url(uri_string()), $formAttr);
  															?>
  															<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  																<label for="">Ramo <span required="" aria-required="true">*</span></label>
  																<input ng-model="ramos.nombre" type="text" id="nombre" name="nombre" class="form-control"  placeholder="" autocomplete="off" data-rule-required="true">
  															</div>

  															<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  																<div class="col-md-4"> 
  																	<h4>Agrupar</h4>

  																	<input type="checkbox" name="" id="isGrouper">

  																	<div id="treeRamos"></div>
  																</div>
  															</div>
  															<div class="row">
  																<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  																	<label for="">Descripci&oacute;n<span required="" aria-required="true">*</span></label>
  																	<input ng-model="ramos.descripcion" type="text" id="descripcion" name="descripcion" class="form-control grouper" value="" placeholder="" autocomplete="off">
  																</div>
  																<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  																	<label for="">Código<span required="" aria-required="true">*</span></label>
  																	<input ng-model="ramos.codigo_ramo" value="" maxlength="3" type="text" id="codigo_ramo" name="codigo_ramo" class="form-control grouper" placeholder="" autocomplete="off" >
  																</div>
  															</div>
  															<div class="row">
  																<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  																	<label for="">Tipo de interes asegurado <span required="" aria-required="true">*</span></span></label>
  																	<select ng-model="ramos.tipo_interes_ramo" name="tipo_interes_ramo" id="tipo_interes_ramo" class="form-control  grouper">
  																		<option value="">Seleccione una opción</option>
  																		<?php foreach($tipo_intereses as $tipo) {?>
  																		<option value="<?php echo $tipo->id?>"><?php echo $tipo->nombre?></option>
  																		<?php }?>
  																	</select>
  																</div>
  																<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
  																	<label for="">Tipo de póliza <span required="" aria-required="true">*</span></label>
  																	<select ng-model="ramos.tipo_poliza_ramo"  name="tipo_poliza_ramo" id="tipo_poliza_ramo"  class="form-control chosen-select  grouper" >
  																		<option value="">Seleccione una opción</option>
  																		<?php foreach($tipo_poliza as $tipo) {?>
  																		<option value="<?php echo $tipo->id?>"><?php echo $tipo->nombre?></option>
  																		<?php }?>
  																	</select>
  																</div>
  															</div>
                                <div class="row">
                                  <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6" style="">
                                    <label for="">Rol(es)<span required="" aria-required="true"> *</label>
                                    <select ng-model="ramos.rol" name="rol" id="rol" class="form-control  grouper">
                                      <option value="">Seleccione una opción</option>
                                      <?php foreach ($roles as $rol) {?>
                                      <option value="<?php print $rol->id ?>"><?php print $rol->nombre ?></option>
                                      <?php }?>
                                     </select>
                                  </div>
                                  <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <label for="">Usuario(s)<span required="" aria-required="true"> *</label>
                                    <select ng-model="ramos.usuario"  name="usuario" id="usuario"  class="form-control  grouper">
                                      <option value="">Seleccione una opción</option>
                                      <?php foreach ($usuarios as $user) {?>
                                      <option value="<?php print $user->id ?>"><?php print $user->nombre .' '. $user->apellido ?></option>
                                      <?php }?>
                                    </select>
                                    
                                  </div>
                                 
                                </div>

                                <input type="hidden" name="codigo" id="codigo" value="0"> 

                                <?php echo form_close(); ?>

                                <div class="row">
                                  <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">&nbsp;</div>
                                  <div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2 pull-right">
                                   <input type="button" id="guardarImpuestoBtn" ng-click="guardarRamo(ramos)" class="btn btn-primary btn-block" value="Guardar" />
                                 </div>
                                 <div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2 pull-right">
                                   <input type="button" ng-click="limpiarFormRamo($target)" id="cancelarImpuestoBtn" class="btn btn-default btn-block" value="Cancelar" />
                                 </div>
                               </div>

                               <?php echo modules::run('configuracion_seguros/ocultotabla_ramos'); ?>

                             </div>


                           </div>


                         </div>

                       </div>
                       <div id="tab-3" class="tab-pane">
                        <div class="panel-body" >
                         <div style="font-size: 2em; margin-left: -25px;">&nbsp; Crear nuevo plan de seguro</div>
                         <div class="hr-line-gray " style="margin-bottom: 50px;"></div>
                         <div role="tabpanel">
                          <!-- Tab panes -->
                          <div class="row tab-content">

                           <div role="tabpanel" class="tab-pane active" id="tabla">
                            <div class="row" style="margin-left: 0px;">
                             <div class="col-lg-12">
                              <div class="">
                               <ul class="nav nav-tabs nuevo tabplanes" style="border-bottom: none">
                                <li class="active" id="tab_aseguradora" style="margin-right: 30px; margin-left: -25px;background-color:#F3F3F4 !important;">
                                 <a id="primertab" data-toggle=""  aria-expanded="true"  style="padding: 0px;">
                                  <div style="font-size:60pt; float: left;padding-left: 5%;color:white; margin-top:-30px;"><b>1</b></div>
                                  <div style="font-size: 1.1em; padding-top: 5%; color:white; "><b>Aseguradora</b></div>
                                </a>
                              </li>
                              <li class="" id="tab_coberturas" style="margin-right: 30px;background-color:#F3F3F4 !important;">
                               <a id="segundotab" data-toggle=""  aria-expanded="false"   style="padding: 0px;">
                                <div style="font-size:60pt; float: left;padding-left: 5%;color:white; margin-top:-30px;"><b>2</b></div>
                                <div style=" font-size: 1.1em; padding-top: 5%;color:white;"><b>Coberturas</b></div>
                              </a>
                            </li>
                            <li class="" id="tab_comision" style="margin-right: 30px;background-color:#F3F3F4 !important;">
                             <a id="tercertab" data-toggle=""  aria-expanded="false"   style="padding: 0px;">
                              <div style="font-size:60pt; float: left;padding-left: 5%;color:white; margin-top:-30px;"><b>3</b></div>
                              <div style=" font-size: 1.1em; padding-top: 5%;color:white;"><b>Comisiones por año</b></div>
                            </a>
                          </li>
                          <li class="" id="tab_confirmar" style="margin-right: 30px;background-color:#F3F3F4 !important;">
                           <a id="cuartotab" data-toggle=""  aria-expanded="false"   style="padding: 0px;">
                            <div style="font-size:60pt; float: left;padding-left: 5%;color:white; margin-top:-30px;"><b>4</b></div>
                            <div style=" font-size: 1.1em; padding-top: 5%;color:white;"><b>Confirmar</b></div>
                          </a>
                        </li>
                      </ul>
                      <div class="tab-content row" ng-controller="configPlanesController">
                        <?php
                        $formAttr = array(
                         'method'       => 'post', 
                         'id'           => 'crearplanesForm',
                         'autocomplete' => 'off'
                         );
                        echo form_open(base_url("planes/crear/planes"), $formAttr);
                        ?>
                        <div class="tab-content">
                         <div id="tab2-1" class="tab-pane active" style="margin-top: -60px; margin-left: -40px">
                          <div class="panel-body" style="padding-top: 100px;">

                           <div style="background: #fff;">
                            <div class="row">
                             <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                              <input id="id_planes" name="id_planes" v-model="id_planes" type="hidden">
                              <input id="vista" name="vista" v-model="vista" type="hidden">
                              <div class="row">
                               <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                <label for="nombre_plan" style="padding-top: 10px;">Nombre del Plan<span required="" aria-required="true">*</span></label>
                                <input type="text" id="nombre_plan"  name="nombre_plan" class="form-control" v-model="nombre_plan" placeholder="" data-rule-required="true" pattern="[A-Za-z]{1,}">
                              </div>
                              <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10 ">
                                <label>Aseguradora<span required="" aria-required="true">*</span></label>
                                <select name="idAseguradora" class="form-control" id="aseguradora" data-rule-required="true" v-model="aseguradora" >
                                 <option value="" selected>Seleccione</option>
                                 <?php foreach($aseguradoras as $aseguradora) {?>
                                 <option value="<?php echo $aseguradora->id?>"><?php echo $aseguradora->nombre?></option>
                                 <?php }?>
                               </select>
                               <input value="" id="hidden_cuenta_pagar" type="hidden">
                               <input type="hidden" name="codigo" id="idRamo" value="0" > 
                             </div>
                             <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10 ">
                              <label for="">Impuesto<span required="" aria-required="true">*</span></label>
                              <select name="impuesto" id="impuesto2" class="form-control" v-model="impuesto" data-rule-required="true">
                               <option value="" selected>Seleccione</option>
                               <?php foreach($impuestos as $impuesto) {?>
                               <option value="<?php echo $impuesto['id']?>"><?php echo $impuesto['value']?></option>
                               <?php }?>
                             </select>
                           </div>
                           <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10 ">
                            <div id="ch_comision_copy" class="panel-heading">
                             <h5 class="panel-title">
                              <input type="checkbox" class="js-switch" name='ch_comision' v-model="ch_comision" id='ch_comision'/>
                              Descuento de comisi&oacute;n en el env&iacute;o de remesas
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 "> 
                       <h4>Ramo</h4>
                       <div id="treeRamosP"></div>
                       <div id="errorramoplanes"><label id="ramoplanes-error" class="error" for="ramoplanes" >Este campo es obligatorio. Seleccione un Ramo.</label></div>
                     </div>
                   </div>
                   <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="col-xs-0 col-sm-6 col-md-10 col-lg-10">&nbsp;</div>

                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                     <button id="siguiente1" class="btn btn-primary btn-block" style="width: 60px;"> Sig. <i class="fa fa-chevron-right"></i></button>
                   </div>
                 </div>

               </div>


             </div>

           </div>

         </div>