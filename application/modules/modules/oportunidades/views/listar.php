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
                                'id' => 'buscarOportunidadesForm',
                                'autocomplete' => 'off'
                            );

                            echo form_open_multipart("", $formAttr);
                            ?>
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar oportunidad</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Nombre de la oportunidad</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control">
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Rango de montos</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                      <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                                      <input type="text" name="monto_desde" id="monto_desde" class="form-control">
                                                      <span class="input-group-addon">a</span>
                                                      <input type="text" name="monto_hasta" id="monto_hasta" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Rango de fechas</label>
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
                                            <label for="">Asignado a</label>
                                            <select name="asignado_a_id" class="form-control select2" id="asignado_a_id">
                                                <option value="">Seleccione</option>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                    <option value="<?php echo $usuario->id ?>"><?php echo $usuario->nombre_completo; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="">Etapa</label>
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
                            <?php echo modules::run('oportunidades/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php echo Modal::config(array(
    "id" => "opcionesModalGrupal",
    "size" => "sm"
))->html();?> <!-- modal cambio de estados -->
<?php echo Modal::config(array("id" => "optionsModal", "size" => "sm"))->html(); ?> <!-- modal opciones -->
