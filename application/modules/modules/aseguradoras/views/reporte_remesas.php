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
                    <div
                        class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar datos</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->

                                    <?php
                                    $formAttr = array(
                                        'method' => 'POST',
                                        'id' => 'buscarForm',
                                        'autocomplete' => 'off'
                                    );
                                    echo form_open(base_url(uri_string()), $formAttr);
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">Rango de fecha</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                        <input type="text" name="desde" id="fecha1"
                                                               class="form-control">
                                                        <span class="input-group-addon">a</span>
                                                        <input type="text" class="form-control" name="hasta"
                                                               id="fecha2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-0 col-sm-0 col-md-5 col-lg-5">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label for="" style="color: transparent;">label</label>
                                            <input type="button" id="searchBtn" class="btn btn-default btn-block"
                                                   value="Filtrar"/>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label for="" style="color: transparent;">label</label>
                                            <input type="button" id="clearBtn" class="btn btn-default btn-block"
                                                   value="Limpiar"/>
                                        </div>
                                    </div>
                                    <div class="row">

                                    </div>
                                    <?php echo form_close(); ?>

                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <!-- /BUSCADOR -->

                        </div>
                    </div>
                </div>
                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="content">
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <div class="pull-left">
                                        <h3>Reporte de Remesas / <?php if (isset($info['aseguradora'])) {
                                                echo $info['aseguradora']['fecha'];
                                            } ;?></h3>
                                    </div>
                                    <div class="pull-right" id="estadoRemesa">
                                        <h3>Estado: Actual</h3>
                                    </div>
                                </div>
                                <div class="ibox-title">
                                    <h5>Datos Generales</h5>
                                    <!--<div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>-->
                                </div>
                                <div class="ibox-content" style="display: block;">

                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">Nombre</label>
                                            <input type="text" name="campo[nombre]" id="campo[nombre]"
                                                   value="<?php if (isset($info['aseguradora'])) {
                                                       echo $info['aseguradora']['nombre'];
                                                   } ?>" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">Contacto</label>
                                            <input type="text" name="campo[contacto]" id="campo[contacto]"
                                                   value="<?php if (isset($info['aseguradora']['contacto'])) {
                                                       echo $info['aseguradora']['contacto'];
                                                   } ?>" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">Teléfono</label>
                                            <input type="text" name="campo[telefono]" id="campo[telefono]"
                                                   value="<?php if (isset($info['aseguradora'])) {
                                                       echo $info['aseguradora']['telefono'];
                                                   } ?>" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">E-mail</label>
                                            <input type="text" name="campo[email]" id="campo[email]"
                                                   value="<?php if (isset($info['aseguradora'])) {
                                                       echo $info['aseguradora']['email'];
                                                   } ?>" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <label for="">Dirección</label>
                                            <input type="text" name="campo[direccion]" id="campo[direccion]"
                                                   value="<?php if (isset($info['aseguradora'])) {
                                                       echo $info['aseguradora']['direccion'];
                                                   } ?>" class="form-control" disabled>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">Rango de fecha</label>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                        <input type="text" name="campo[fecha_desde]"
                                                               id="campo[fecha_desde]" class="form-control" disabled
                                                               value="<?php if (isset($info['aseguradora'])) {
                                                                   echo $info['aseguradora']['fecha_desde'];
                                                               } ?>">
                                                        <span class="input-group-addon"> </span>
                                                        <input type="text" class="form-control"
                                                               name="campo[fecha_hasta]" id="campo[fecha_hasta]"
                                                               disabled value="<?php if (isset($info['aseguradora'])) {
                                                            echo $info['aseguradora']['fecha_hasta'];
                                                        } ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">


                                    </div>
                                </div>
                                <div class="ibox-title">
                                    <h5>Detalle</h5>
                                    <!--<div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>-->
                                </div>
                                <div class="ibox-content" style="display: block;">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover"
                                                   id="remesasGrid">
                                                <thead>
                                                <tr>
                                                    <th>Factura #</th>
                                                    <th>Asegurado</th>
                                                    <th>Ramo / Riesgo</th>
                                                    <th>Nº Poliza</th>
                                                    <th>Recibo</th>
                                                    <th>Primas</th>
                                                    <th>Pago</th>
                                                    <th>%</th>
                                                    <th>Comisión</th>
                                                    <th>Saldo</th>

                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label for="">Estado</label>
                                            <select class="form-control estado" name="campo[estado]">
                                                <option value="Actual">Actual</option>
                                                <option value="Pagado">Pagado</option>
                                                <option value="Vencido">Vencido</option>
                                            </select>
                                        </div>
                                        <div class="col-xs-0 col-sm-0 col-md-3 col-lg-3">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label for="" style="color: transparent;">label</label>
                                            <a type="button" href="<?php echo base_url('aseguradoras/listar'); ?>"
                                               class="btn btn-default btn-block">Cancelar</a>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label for="" style="color: transparent;">label</label>
                                            <input type="button" id="saveBtn" class="btn btn-primary btn-block"
                                                   value="Guardar"/>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                            <label for="" style="color: transparent;">label</label>
                                            <input type="button" id="exportBtn" class="btn btn-green btn-block"
                                                   value="Exportar"/>
                                        </div>
                                    </div>

                                    <div class="row"
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

