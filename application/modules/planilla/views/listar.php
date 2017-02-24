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


                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Planilla</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" class='collapsed'  style="display: none;">
                                    <!-- Inicia campos de Busqueda -->

                                    		<?php
			                        $formAttr = array(
			                            'method'        => 'POST',
			                            'id'            => 'buscarPlanillaForm',
			                            'autocomplete'  => 'off'
			                          );
			                         echo form_open(base_url(uri_string()), $formAttr);
			                        ?>


                                    <div class="row">
                                      <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                          <label for="No_oc">Número de planilla</label><br>

                                               <input type="text" name="codigo" id="codigo" class="form-control">



                                      </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
							            	   <label for="nombre">Centro Contable</label>
                                           <select id="centro_contable_id" name="centro_contable" class="form-control select2" style="width:375px;" >
								                <option value="">Seleccione</option>
								                <?php
								                if(!empty($centros))
								                {
									                foreach ($centros AS $centro)
									                {
									               		echo '<option value="'. $centro['id'] .'">'. $centro['nombre'] .'</option>';
									                }
								                }
								                ?>
							                </select>
										</div>

                                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                           <label for="estado">&Aacute;rea de negocio</label><br>
                                            <select id="departamento_id" name="departamento_id" class="form-control select2"  style="width:375px;">
								                <option value="">Seleccione</option>
								                <?php
								                if(!empty($areas_negocio))
								                {
									                foreach ($areas_negocio AS $area)
									                {
									               		echo '<option value="'. $area['id'] .'">'. $area['nombre'] .'</option>';
									                }
								                }
								                ?>
							                </select>
                                        </div>


                                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                            <label for="nombre">Estado</label>
                                          <select id="estado_id" name="estado" class="form-control select2"  style="width:375px;">
                                        <option value="">Seleccione</option>
                                        <?php
                                        if(!empty($estados))
                                        {
                                        foreach ($estados AS $estado)
                                        {
                                        echo '<option value="'. $estado['id_cat'].'">'. $estado['etiqueta'] .'</option>';
                                        }
                                        }
                                        ?>
                                        </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                          <label for="rango_fecha">Rango de fechas</label><br>
                                           <div class="form-inline">
                                        <div class="form-group">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <input type="text" name="desde" id="fecha1" class="form-control">
                                              <span class="input-group-addon">a</span>
                                              <input type="text" class="form-control" name="hasta" id="fecha2">
                                            </div>
                                        </div>
                                    </div>
                                            <!-- <input type="text" id="rango_fecha" class="form-control" value="" readonly="readonly">-->
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
                                    <?php echo form_close(); ?>
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('planilla/ocultotablaplanilla'); ?>

                            <!-- /JQGRID -->
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
