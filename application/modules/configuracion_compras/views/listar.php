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
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div class="panel-heading white-bg">
                            <span class="panel-title"></span>
                            <ul class="nav nav-tabs nav-tabs-xs formTabs">
                                <li class="dropdown pull-right tabdrop hide">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-align-justify"></i>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu"></ul>
                                </li>
                                <li class="active">
                                    <a data-toggle="tab" href="#catalogos">Cat&aacute;logos</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#chequeras">Chequeras</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#documentos">Documentos</a>
                                </li>
                            </ul>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="catalogos">
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear tipo proveedor</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="categoria">Tipo<span style="color:red;"> *</span></label>
                                            <input type="text" id="tipo" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                            <label for="descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="descripcion" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarTipoBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarTipoBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoTipo" value="crear" data-uuid="">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('configuracion_compras/ocultotablaTipo'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="catalogos">
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear categor&iacute;a proveedor</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="categoria">Nombre<span style="color:red;"> *</span></label>
                                        <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                        <label for="descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                        <input type="text" id="descripcionCat" class="form-control" value="" placeholder="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <input type="button" id="cancelarCategoriaBtn" class="btn btn-default btn-block" value="Cancelar" />
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <input type="button" id="guardarCategoriaBtn" class="btn btn-success btn-block" value="Guardar" />
                                    </div>
                                    <input type="hidden" id="modoCategoria" value="crear" data-uuid="">
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- JQGRID -->
                                        <?php echo modules::run('configuracion_compras/ocultotablaCategoria'); ?>
                                    </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="chequeras">

                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Detalle de chequera</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="nombre">Nombre de chequera</label>

                                            <input type="text" id="nombre_chequera" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="cuenta_banco">Cuenta de banco</label>
                                            <select id="cuenta_banco" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($cuentas_bancos as $activo):?>
                                                    <option value="<?php echo $activo->cuenta_id?>"><?php echo $activo->cuenta->nombre?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="cheque_inicial">N&uacute;mero de cheque inicial</label>

                                            <input type="text" id="cheque_inicial" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="cheque_final">N&uacute;mero de cheque final</label>

                                            <input type="text" id="cheque_final" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="proximo_cheque">Pr&oacute;ximo cheque</label>

                                            <input type="text" id="proximo_cheque" class="form-control" value="" placeholder="">
                                        </div>


                                    </div>

                                    <div class="hr-line-dashed m-t-xs"></div>


                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="ancho">Ancho del cheque</label>
                                            <input type="text" id="ancho" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="alto">Alto del cheque</label>
                                            <input type="text" id="alto" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="izquierda">Compensar izquierda</label>
                                            <input type="text" id="izquierda" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="derecha">Compensar derecha</label>
                                            <input type="text" id="derecha" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="arriba">Comprensar arriba</label>
                                            <input type="text" id="arriba" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="abajo">Compensar abajo</label>
                                            <input type="text" id="abajo" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="posicion">Posici&oacute;n del cheque</label>
                                            <input type="text" id="posicion" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="imprimirPruebaCheque"></label>
                                            <button type="button" id="imprimirPruebaCheque" class="btn btn-success" style="width:100%;background-color: #5cb85c;border-color: #4cae4c;">
                                                <i class="fa fa-print"></i> Imprimir Prueba
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarChequeraBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarChequeraBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoChequera" value="crear" data-uuid="">
                                    </div>
                                    <!-- Termina campos -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('configuracion_compras/ocultotablaChequeras'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div role="tabpanel" class="tab-pane active" id="documentos">
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear tipo de documento</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="categoria">Tipo de documento<span style="color:red;"> *</span></label>
                                            <input type="text" id="tipoDoc" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                            <label for="descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="descripcionDoc" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarTipoDocBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarTipoDocBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoTipo" value="crear" data-uuid="">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('configuracion_compras/ocultotablaTipoDoc'); ?>
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
$formAttr = array('method' => 'POST', 'id' => 'exportarTablasConfig','autocomplete'  => 'off');
echo form_open(base_url('configuracion_compras/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<input type="hidden" name="tabla" id="tabla" value="" />
<?php echo form_close(); ?>
<?php

    echo    Modal::config(array(
                "id"    => "optionsModal",
                "size"  => "sm"
            ))->html();

?>
