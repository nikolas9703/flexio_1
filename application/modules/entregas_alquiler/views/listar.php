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
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <?php
                            $formAttr = array(
                                'method' => 'POST',
                                'id' => 'buscarEntregasAlquilerForm',
                                'autocomplete' => 'off'
                            );

                            echo form_open_multipart("", $formAttr);
                            ?>
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar entrega</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. Entrega</label>
                                            <input type="text" name="codigo" id="codigo" class="form-control">
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Fecha de entrega</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                      <input type="text" name="fecha_desde" id="fecha_desde" class="form-control">
                                                      <span class="input-group-addon">a</span>
                                                      <input type="text" name="fecha_hasta" id="fecha_hasta" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">No. Contrato</label>
                                            <input type="text" name="no_contrato" id="no_contrato" class="form-control">
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Cliente</label>
                                            <select name="cliente_id" class="form-control select2" id="cliente_id">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($clientes as $cliente): ?>
                                                    <option value="<?php echo $cliente->id ?>"><?php echo $cliente->nombre ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Centro de facturaci&oacute;n</label>
                                            <select name="centro_facturacion_id" class="form-control select2" id="centro_facturacion_id">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($centros_facturables as $centro_facturacion): ?>
                                                    <option value="<?php echo $centro_facturacion->id ?>"><?php echo $centro_facturacion->nombre ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select name="estado_id" class="form-control select2" id="estado_id">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($estados as $estado): ?>
                                                    <option value="<?php echo $estado->id ?>"><?php echo $estado->nombre ?></option>
                                                <?php endforeach; ?>
                                            </select>
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
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('entregas_alquiler/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php echo Modal::config(array("id" => "optionsModal", "size" => "sm"))->html(); ?> <!-- modal opciones -->
