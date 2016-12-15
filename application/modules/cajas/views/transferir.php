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

                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'transferirForm',
                    'autocomplete' => 'off',
                    'enctype' => 'multipart/form-data',
                    'ng-controller' => 'TransferirACajaController'
                );
                echo form_open(base_url(uri_string()), $formAttr);
                ?>
                <div class="ibox">
                    <div class="ibox-title border-bottom">
                        <h5>Transferir a caja</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div style="display: block; border:0px" class="ibox-content m-b-sm">
                        <div class="row">

                            <!--<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre ">
                                <label>N&uacute;mero de transferencia</label>
                                <input type="text" id="nombre" class="chosen-select form-control" value="" name="numero" disabled="disbaled" />
                            </div>-->
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Transferir de cuenta de banco <span class="required">*</span></label>
                                <select id="cuenta_id" class="chosen-select form-control" name="cuenta_id"  data-rule-required="true" data-msg-required="">
                                    <option value="">Seleccione</option>
                                    <option ng-repeat="cuentas_banco in cuentas_bancosList track by $index" value="{{cuentas_banco.cuenta.id && ''|| cuentas_banco.cuenta.id}}">{{cuentas_banco.cuenta.nombre && ''|| cuentas_banco.cuenta.nombre}}</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Monto <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" id="monto" ng-model="transferir.monto" class="form-control" value="" name="monto" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" data-rule-required="true" data-msg-required="" />
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Fecha <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="fecha" ng-model="transferir.fecha" readonly="readonly" class="form-control" value="" name="fecha">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-noline tabla-dinamica" id="pagosTable">
                                        <tbody>
                                            <tr id="metodopago0">
                                                <td class="textouno0" width="20%"><input type="text" id="nombre0" class="form-control" value="Metodo de pago" name="metodo[0][text]" readonly="readonly" style="border:0px;" /></td>
                                                <td class="metodo_pago_id0" width="25%">
                                                    <select id="tipo_pago_id0" class="chosen-select form-control" name="tipospago[0][tipo_pago_id]" data-rule-required="true" data-msg-required="" />
                                        <option value="">Seleccione</option>
                                        <?php
                                        if (!empty($tipo_pagos)) {
                                            foreach ($tipo_pagos AS $tipo_pago) {
                                                echo '<option value="' . $tipo_pago["id"] . '">' . $tipo_pago["valor"] . '</option>';
                                            }
                                        }
                                        ?>
                                        </select>
                                        <input type="hidden" id="no_cheque0" name="tipospago[0][no_cheque]" placeholder="N&uacute;mero de Cheque" value="" class="form-control m-t-sm hide" disabled="disabled" field-new-colum="false" />
                                        </td>
                                        <td class="textodos0" width="20%"><input type="text" id="textodos0" class="form-control" value="Total pagado" name="" readonly="readonly" style="border:0px;"></td>
                                        <td class="monto_pagar0" width="25%">
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" id="monto_a_pagar0" class="form-control" value="" name="tipospago[0][monto]" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" ng-blur="CalcularMontoTotal($event)" data-rule-required="true" data-msg-required="" />
                                            </div>
                                            <input type="hidden" id="banco0" name="tipospago[0][banco]"  placeholder="Banco" value="" class="form-control m-t-sm hide" disabled="disabled" field-new-colum="false" />
                                        </td>
                                        <td width="5%"><button agrupador="transferir" class="btn btn-default btn-block eliminarPagoBtn" type="button"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button></td>
                                        <td width="5%"><button agrupador="transferir" class="btn btn-default btn-block agregarPagosBtn" type="button"><i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button></td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td class="monto_pagar0" width="25%">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">$</span>
                                                        <input type="text" id="total_a_pagar" class="form-control" value="{{total}}" name="total_a_pagar" readonly="readonly"/>
                                                    </div>
                                                    <label class="label m-t-xs btn-primary p-xs col-xs-12 col-sm-12 col-md-12 col-lg-12">Total</label>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('cajas/listar'); ?>" class="btn btn-default btn-block">Cancelar</a></div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <button type="button" id="guardarBtn" class="btn btn-primary btn-block {{disabledBtn}}" ng-bind-html="guardarBtn" ng-click="guardar($event)" ng-disabled="configurado.length == 0">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="caja_id" value="<?php echo!empty($caja_id) ? $caja_id : ""; ?>" />
                <?php echo form_close(); ?>

            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
