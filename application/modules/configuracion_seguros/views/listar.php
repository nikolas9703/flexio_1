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
                 <div role="tabpanel" class="tab-pane active" id="tabla">

                   <div class="row">
                    <div class="col-lg-12">
                        <div class="">
                            <ul class="nav nav-tabs">
                                <li class="active" id="tab_ramos"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="generales">Ramos</a></li>
                                <li class="" id="tab_planes"><a data-toggle="tab" href="#tab-3" aria-expanded="false"  data-targe="beneficios">Planes</a></li>
                                <li class="" id="tab_catalogo"><a data-toggle="tab" href="#tab-3" aria-expanded="false"  data-targe="beneficios">Catálogos</a></li>
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
                                                    <input type="checkbox" name="" id="isGroup">
                                                    <div id="treeRamos">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <label for="">Descripci&oacute;n</label>
                                                    <input ng-model="ramos.descripcion" type="text" id="descripcion_ramo" name="descripcion" class="form-control" value="" placeholder="" autocomplete="off" >
                                                </div>
                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <label for="">Código</label>
                                                    <input ng-model="ramos.codigo_ramo" value="" maxlength="3" type="text" id="codigo_ramo" name="codigo_ramo" class="form-control" placeholder="" autocomplete="off" >
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <label for="">Tipo de interes asegurado <span class="requerido" required="" aria-required="true"></span></label>
                                                    <select  aria-required="true"  class="chosen-select"  data-placeholder="Seleccione" multiple="multiple" name="tipo_interes_ramo" id="tipo_interes_ramo">
                                                        <option value=""></option>
                                                        <?php foreach($tipo_intereses as $tipo) {?>
                                                        <option value="<?php echo $tipo->id?>"><?php echo $tipo->nombre?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <label for="">Tipo de póliza <span class="requerido" required="" aria-required="true"></span></label>
                                                    <select ng-model="ramos.tipo_poliza_ramo"  name="tipo_poliza_ramo" id="tipo_poliza_ramo"  class="form-control chosen "  >
                                                        <option value=""></option>
                                                        <?php foreach($tipo_poliza as $tipo) {?>
                                                        <option value="<?php echo $tipo->id?>"><?php echo $tipo->nombre?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                                    <label>Tipo <span aria-required="true" required="">*</span></label>
                                                    <select aria-required="true" style="display: none;" name="campo[tipo]" class="chosen-select" id="tipo" data-rule-required="true">
                                                        <option value="">Seleccione</option>

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

                                            <!-- JQGRID -->
                                            <?php print modules::run('Configuracion_seguros/ocultotabla'); ?>
                                            <!-- /JQGRID -->

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
                                                        echo form_open(base_url("configuracion_seguros/configuracion/planes"), $formAttr);
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
                                                                                        <label for="nombre_plan" style="padding-top: 10px;">Nombre del Plan</label>
                                                                                        <input type="text" id="nombre_plan"  name="nombre_plan" class="form-control" v-model="nombre_plan" placeholder="" >
                                                                                    </div>
                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10 ">
                                                                                        <label>Aseguradora<span required="" aria-required="true">*</span></label>
                                                                                        <select name="idAseguradora" class="form-control" id="aseguradora" data-rule-required="true" v-model="aseguradora" >
                                                                                            <option value="">Seleccione</option>
                                                                                            <?php foreach($aseguradoras as $aseguradora) {?>
                                                                                            <option value="<?php echo $aseguradora->id?>"><?php echo $aseguradora->nombre?></option>
                                                                                            <?php }?>
                                                                                        </select>
                                                                                        <input value="" id="hidden_cuenta_pagar" type="hidden">
                                                                                        <input type="hidden" name="codigo" id="idRamo" value="0" > 
                                                                                    </div>
                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10 ">
                                                                                        <label for="">Impuesto</label>
                                                                                        <select name="impuesto" id="impuesto" class="form-control" v-model="impuesto" >
                                                                                            <option value=""></option>
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
                                                          <div id="tab2-2" class="tab-pane" style="margin-top: -60px; margin-left: -40px">
                                                            <div class="panel-body" style="padding-top: 100px;">
                                                                <div style="background: #fff;">
                                                                    <div class="row">
                                                                        <div class="col-lg-10" id="tabla_planes">
                                                                            <div class="row">
                                                                                <div >
                                                                                    <table  id="tabla_fact" style="width: 100%;">
                                                                                        <thead>
                                                                                            <tr >
                                                                                                <th style="width: 95%;">

                                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 30px;margin-bottom: 0px !important;padding-left: 0px;">
                                                                                                        <label for="nombre_plan">Coberturas(s)</label>
                                                                                                    </div>
                                                                                                </th>
                                                                                                <th style="width: 5%;"></th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <?php $tamanio=0; if(isset($coberturas_data) && !empty($coberturas_data)){ ?>
                                                                                            <?php if(!empty($coberturas_data)){ ?>
                                                                                            <?php $tamanio=  count($coberturas_data);  ?>
                                                                                            <?php foreach ($coberturas_data as $key => $cobertura) { ?>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                        <input  type="text" id="nombre_plan"  name="coberturas[]" onkeyup="planes.copyTabla('tabla_planes','tabla_final');" class="form-control coberturas" value="<?=$cobertura->nombre ?>" placeholder=""  >
                                                                                                    </div>
                                                                                                </td>

                                                                                                <td>
                                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                                                                                                        <?php if($tamanio==1): ?>
                                                                                                            <a onclick="planes.agregarfila(this,'tabla_fact');" class="btn btn-default" id="agregarbtn" style="margin-top: 5px;">+</a>
                                                                                                            <a onclick="planes.eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: 5px;display: none">-</a>
                                                                                                        <?php else: ?>
                                                                                                            <?php if($tamanio==($key+1)): ?>
                                                                                                                <a onclick="planes.agregarfila(this,'tabla_fact');" class="btn btn-default" id="agregarbtn" style="margin-top: 5px;">+</a>
                                                                                                                <a onclick="planes.eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: 5px;display: none">-</a>
                                                                                                            <?php else: ?>
                                                                                                                <a onclick="planes.agregarfila(this,'tabla_fact');" class="btn btn-default" id="agregarbtn" style="margin-top: 5px;display: none">+</a>
                                                                                                                <a onclick="planes.eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: 5px;">-</a>
                                                                                                            <?php endif; ?>


                                                                                                        <?php endif; ?>
                                                                                                    </div>    

                                                                                                </td> 
                                                                                            </tr>
                                                                                            <?php } ?> 
                                                                                            <?php } ?>
                                                                                            <?php } ?>
                                                                                            <?php if($tamanio==0){ ?>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                        <input  type="text" id="nombre_plan"  name="coberturas[]" onkeyup="planes.copyTabla('tabla_planes','tabla_final');" class="form-control coberturas" value="" placeholder=""  >
                                                                                                    </div>
                                                                                                </td>

                                                                                                <td>
                                                                                                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                                                                                                        <a onclick="planes.agregarfila(this,'tabla_fact');" class="btn btn-default" id="agregarbtn" style="margin-top: 5px;">+</a>
                                                                                                        <a onclick="planes.eliminarfila(this);" class="btn btn-default" id="eliminarbtn" style="margin-top: 5px;display: none">-</a>
                                                                                                    </div>    

                                                                                                </td> 
                                                                                            </tr> 
                                                                                            <?php } ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>



                                                                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                            <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2">
                                                                              <button id="anterior1" class="btn btn-default btn-block" style="width: 60px;"><i class="fa fa-chevron-left"></i> Ant.</button>
                                                                          </div>
                                                                          <div class="col-xs-0 col-sm-4 col-md-8 col-lg-8">&nbsp;</div>

                                                                          <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2">
                                                                              <button id="siguientedos" class="btn btn-primary btn-block" style="width: 60px;"> Sig. <i class="fa fa-chevron-right"></i></button>
                                                                          </div>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                      <div id="tab2-3" class="tab-pane" style="margin-top: -60px; margin-left: -40px">
                                                        <div class="panel-body" style="padding-top: 100px;">

                                                            <div style="background: #fff;">
                                                                <div class="row">
                                                                    <div class="col-lg-11" id="tabla_comis">
                                                                        <div class="row">
                                                                            <table  id="tabla_comisiones" style="width: 100%;">
                                                                                <thead>
                                                                                    <tr >
                                                                                        <th class="th_anio" style="width: 20%;display: none;">

                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 30px;margin-bottom: 0px !important;padding-left: 0px;">
                                                                                                <label for="nombre_1">Año:</label>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th class="th_inicio" style="width: 20%;">

                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 30px;margin-bottom: 0px !important;padding-left: 0px;">
                                                                                                <label for="nombre_1">Año inicio</label>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th class="th_fin" style="width: 20%;">
                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 30px;margin-bottom: 0px !important;padding-left: 0px;">
                                                                                                <label for="nombre_2">Año fin</label>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th style="width: 20%;">
                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 30px;margin-bottom: 0px !important;padding-left: 0px;">
                                                                                                <label for="nombre_3">Comisión</label>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th style="width: 20%;">
                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 30px;margin-bottom: 0px !important;padding-left: 0px;">
                                                                                                <label for="nombre_4">Sobrecomisión</label>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th style="width: 5%;"></th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php $tamanio2=0; if(isset($comisiones_data) && !empty($comisiones_data)){ ?>
                                                                                    <?php if(!empty($comisiones_data)){ ?>
                                                                                    <?php $tamanio2=  count($comisiones_data);  ?>
                                                                                    <?php foreach ($comisiones_data as $key => $comision) { ?>
                                                                                    <tr data-num="<?=$key ?>">
                                                                                        <th class="th_anio" style="display: none;">
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <div class="input-group">
                                                                                                    <input type="text" id="anio_final" class="form-control " <?php if($tamanio2==($key+1)): ?> <?php if("+"==$comision->fin): ?>value="<?=$comision->inicio ?>+"<?php else: ?>value="<?=$comision->inicio ?>" <?php endif; ?><?php else: ?>value="<?=$comision->inicio ?>" <?php endif; ?> >
                                                                                                </div>
                                                                                            </div>

                                                                                        </th>
                                                                                        <td class="th_inicio">
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <select name="anio_inicio[0]" id="anio_inicio" class="form-control" onchange="planes.anioInicio(this);" required="">
                                                                                                    <?php for($i=$comision->inicio;$i<21;$i++){ ?>
                                                                                                    <option value="<?=$i?>" <?php if($i==$comision->inicio): ?> selected="" <?php endif; ?>><?=$i?></option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="th_fin">
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <select name="anio_fin[0]" id="anio_fin" class="form-control anio_fin" onchange="planes.anioFin(this);" required="">
                                                                                                    <option value=""></option>
                                                                                                    <option value="+" <?php if("+"==$comision->fin): ?> selected="" <?php endif; ?>>+</option>
                                                                                                    <?php for($i=1;$i<21;$i++){ ?>
                                                                                                    <option value="<?=$i?>" <?php if($i==$comision->fin): ?> selected="" <?php endif; ?> ><?=$i?></option>
                                                                                                    <?php } ?>

                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <div class="input-group">
                                                                                                    <input  type="text" id="nombre_plan"  name="p_comision[0]" onkeyup="planes.copyTabla('tabla_comis','tabla_final_comisiones');" class="form-control coberturas" value="<?=$comision->comision ?>" placeholder="" >
                                                                                                    <span class="input-group-addon">%</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <div class="input-group">
                                                                                                    <input  type="text" id="nombre_plan"  name="p_sobre_comision[0]" onkeyup="planes.copyTabla('tabla_comis','tabla_final_comisiones');" class="form-control coberturas" value="<?=$comision->sobre_comision ?>" placeholder="" >
                                                                                                    <span class="input-group-addon">%</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>

                                                                                        <td>
                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                                                                                                <?php if($tamanio2==1): ?>
                                                                                                    <a onclick="planes.agregarfila(this,'tabla_comisiones');" class="btn btn-default btn-tabla-agregar" id="agregarbtn" style="margin-top: 5px;">+</a>
                                                                                                    <a onclick="planes.eliminarfila(this);" class="btn btn-default btn-tabla-eliminar" id="eliminarbtn" style="margin-top: 5px;display: none">-</a>
                                                                                                <?php else: ?>
                                                                                                    <?php if($tamanio2==($key+1)): ?>
                                                                                                        <a onclick="planes.agregarfila(this,'tabla_comisiones');" class="btn btn-default btn-tabla-agregar" id="agregarbtn" style="margin-top: 5px;">+</a>
                                                                                                        <a onclick="planes.eliminarfila(this);" class="btn btn-default btn-tabla-eliminar" id="eliminarbtn" style="margin-top: 5px;display: none">-</a>
                                                                                                    <?php else: ?>
                                                                                                        <a onclick="planes.agregarfila(this,'tabla_comisiones');" class="btn btn-default btn-tabla-agregar" id="agregarbtn" style="margin-top: 5px;display: none">+</a>
                                                                                                        <a onclick="planes.eliminarfila(this);" class="btn btn-default btn-tabla-eliminar" id="eliminarbtn" style="margin-top: 5px;">-</a>
                                                                                                    <?php endif; ?>


                                                                                                <?php endif; ?>
                                                                                            </div>    

                                                                                        </td> 
                                                                                    </tr>
                                                                                    <?php } ?> 
                                                                                    <?php } ?>
                                                                                    <?php } ?>
                                                                                    <?php if($tamanio2==0){ ?>
                                                                                    <tr data-num="1">
                                                                                        <th class="th_anio" style="display: none;">
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <div class="input-group">
                                                                                                    <input type="text" id="anio_final" class="form-control " value=""  >
                                                                                                </div>
                                                                                            </div>

                                                                                        </th>
                                                                                        <td class="th_inicio">
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <select name="anio_inicio[0]" id="anio_inicio" class="form-control" onchange="planes.anioInicio(this);" required="">
                                                                                                    <option value=""></option>
                                                                                                    <?php for($i=1;$i<21;$i++){ ?>
                                                                                                    <option value="<?=$i?>"><?=$i?></option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="th_fin">
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <select name="anio_fin[0]" id="anio_fin" class="form-control anio_fin" onchange="planes.anioFin(this);" required="">
                                                                                                    <option value=""></option>
                                                                                                    <option value="+">+</option>
                                                                                                    <?php for($i=1;$i<21;$i++){ ?>
                                                                                                    <option value="<?=$i?>"><?=$i?></option>
                                                                                                    <?php } ?>

                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <div class="input-group">
                                                                                                    <input  type="text" id="p_comision"  name="p_comision[0]" onkeyup="planes.copyTabla('tabla_comis','tabla_final_comisiones');" class="form-control coberturas" value="" placeholder="" >
                                                                                                    <span class="input-group-addon">%</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td>
                                                                                            <div class="form-group col-xs-11 col-sm-11 col-md-11 col-lg-11" style="margin-bottom: 0px !important;padding-left: 0px;" >
                                                                                                <div class="input-group">
                                                                                                    <input  type="text" id="p_sobre_comision"  name="p_sobre_comision[0]" onkeyup="planes.copyTabla('tabla_comis','tabla_final_comisiones');" class="form-control coberturas" value="" placeholder="" >
                                                                                                    <span class="input-group-addon">%</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>

                                                                                        <td>
                                                                                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px !important;" >
                                                                                                <a onclick="planes.agregarfila(this,'tabla_comisiones');" class="btn btn-default btn-tabla-agregar" id="agregarbtn" style="margin-top: 5px;">+</a>
                                                                                                <a onclick="planes.eliminarfila(this);" class="btn btn-default btn-tabla-eliminar" id="eliminarbtn" style="margin-top: 5px;display: none">-</a>
                                                                                            </div>    

                                                                                        </td> 
                                                                                    </tr> 
                                                                                    <?php } ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-top: 15px;">
                                                                        <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2">
                                                                          <button id="anterior2" class="btn btn-default btn-block" style="width: 60px;"><i class="fa fa-chevron-left"></i> Ant.</button>
                                                                      </div>
                                                                      <div class="col-xs-0 col-sm-4 col-md-8 col-lg-8">&nbsp;</div>

                                                                      <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2">
                                                                          <button id="siguientetres" class="btn btn-primary btn-block" style="width: 60px;"> Sig. <i class="fa fa-chevron-right"></i></button>
                                                                      </div>
                                                                  </div>
                                                              </div>


                                                          </div>

                                                      </div>
                                                  </div>
                                                  <div id="tab2-4" class="tab-pane" style="margin-top: -60px; margin-left: -40px">
                                                    <div class="panel-body" style="padding-top: 100px;">

                                                        <div style="background: #fff;">
                                                            <div class="row">
                                                                <div  id="mensaje"></div>
                                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <div class="row">
                                                                        <div class="form-group col-xs-12 col-sm-11 col-md-11 col-lg-11">
                                                                            <label for="nombre_plan" style="padding-top: 10px;">Nombre del Plan</label>
                                                                            <input type="text" id="nombre_plan_final"  name="nombre_plan_final" class="form-control" v-model="nombre_plan_final" placeholder="" data-rule-required="true"  disabled="">
                                                                        </div>
                                                                        <div class="form-group col-xs-12 col-sm-11 col-md-11 col-lg-11 ">
                                                                            <label>Aseguradora</label>
                                                                            <select name="campo" class="form-control" id="aseguradora_final" v-model="aseguradora_final" disabled="">
                                                                                <option value="">Seleccione</option>
                                                                                <?php foreach($aseguradoras as $aseguradora) {?>
                                                                                <option value="<?php echo $aseguradora->id?>"><?php echo $aseguradora->nombre?></option>
                                                                                <?php }?>
                                                                            </select>
                                                                            <input value="" id="hidden_cuenta_pagar" type="hidden">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <div class="col-md-11" style="padding-top: 10px;">
                                                                        <h4>Ramo</h4>
                                                                        <input type="text" id="ramo_plan_final"  name="ramo_plan_final" class="form-control"  value="" placeholder="" data-rule-required="true"  disabled="">
                                                                    </div>
                                                                </div>

                                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <div class="form-group col-md-11">
                                                                        <label for="">Impuesto</label>
                                                                        <select name="impuesto_final" id="impuesto_final" class="form-control" v-model="impuesto_final" disabled="">
                                                                            <option value=""></option>
                                                                            <?php foreach($impuestos as $impuesto) {?>
                                                                            <option value="<?php echo $impuesto['id']?>"><?php echo $impuesto['value']?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>

                                                                </div>


                                                                <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" id="tabla_final">
                                                                </div>
                                                                <div class="row col-xs-12 col-sm-6 col-md-6 col-lg-6" id="tabla_final_comisiones">
                                                                </div>
                                                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                    <div id="ch_comision_paste" class="form-group col-xs-12 col-sm-11 col-md-11 col-lg-11 ">
                                                                        <div iclass="panel-heading">
                                                                            <h5 class="panel-title">
                                                                                <input type="checkbox" class="js-switch" name='ch_comision_final' id='ch_comision_final' readonly=""/>
                                                                                Descuento de comisi&oacute;n en el env&iacute;o de remesas
                                                                            </h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                    <div class="col-xs-0 col-sm-6 col-md-10 col-lg-10">&nbsp;</div>

                                                                    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                        <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2">
                                                                          <button id="anterior3" class="btn btn-default btn-block" style="width: 60px;"><i class="fa fa-chevron-left"></i> Ant.</button>
                                                                      </div>
                                                                      <div class="col-xs-0 col-sm-4 col-md-8 col-lg-8">&nbsp;</div>

                                                                      <div class="form-group col-xs-12 col-sm-4 col-md-2 col-lg-2">
                                                                        <button type="button" ng-click="guardarPlanes()" id="" class="btn btn-primary btn-block" style="width: 100px;"> Confirmar</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>

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
            </div>
        </div>
    </div>

</div>


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

echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
    ))->html();
echo Modal::config(array(
	"id" => "estadoRamoModal",
	"size" => "sm"
    ))->html();
    ?>
