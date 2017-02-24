<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    //dd($transferir->pagos);
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
                        <h5>Transferir a caja:</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div style="display: block; border:0px" class="ibox-content m-b-sm">
                        <div class="row">

                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre ">
                                <label>N&uacute;mero de transferencia</label>
                                <input type="text" id="nombre" class="chosen-select form-control" value="<?php echo $transferir->numero; ?>" name="numero" disabled="disbaled" />
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Transferir de cuenta de banco <span class="required">*</span></label>

                                    <select id="cuenta_id" class="chosen-select form-control" name="cuenta_id" ng-model="transferir.cuenta_id" data-rule-required="true" data-msg-required="" disabled="disbaled">
                                        <option value="">Seleccione</option>
                                         <option ng-repeat="cuentas_banco in cuentas_bancosList track by $index" value="{{cuentas_banco.cuenta.id && ''|| cuentas_banco.cuenta.id}}">{{cuentas_banco.cuenta.nombre && ''|| cuentas_banco.cuenta.nombre}}</option>

                                    </select>

                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Monto <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                        <input type="text" id="monto" class="form-control" value="<?php echo $transferir->monto; ?>" name="monto" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" data-rule-required="true" data-msg-required="" disabled="disbaled"/>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Fecha <span class="required">*</span></label>
                                <div class="input-group"transferir_fecha>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="fecha"  readonly="readonly" class="form-control" value="<?php echo $transferir->fecha; ?>" name="fecha" disabled="disbaled"/>
                                </div>
                            </div>

                        </div>
                          <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Estado <span class="required">*</span></label>
                                <select id="estado" class="chosen-select form-control" name="estado"  ng-model="transferir.estado" data-rule-required="true" data-msg-required="" >
                                    <option value="">Seleccione</option>
                                    <option ng-repeat="valores in estados" value="{{valores.etiqueta}}" >{{valores.valor}}</option>

                                </select>
                            </div>
                          </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-noline tabla-dinamica" id="pagosTable">

                                            <tbody>
                                                <?php foreach($transferir->pagos as $i=>$pago){?>
                                                <tr id="metodopago0">
                                                    <td class="textouno0" width="20%"><input type="text" id="nombre0" class="form-control" value="Metodo de pago" name="metodo[0][text]" readonly="readonly" style="border:0px;" /></td>
                                                    <td class="metodo_pago_id0" width="25%">
                                                        <select id="tipo_pago_id0" class="chosen-select form-control" name="tipospago[0][tipo_pago_id]" data-rule-required="true" data-msg-required="" disabled="disbaled"/>
                                            <option value="">Seleccione</option>
                                            <?php
                                            // print_r($transferir_pagos);
                                            if (!empty($tipo_pagos)) {
                                                foreach ($tipo_pagos AS $tipo_pago) {
                                            ?>
                                                    <option  <?php echo  $tipo_pago["id"]==$pago->tipo_pago_id?'selected':'' ?> value=" <?php echo $tipo_pago["id"]?> "> <?php echo $tipo_pago["valor"]?> </option>
                                            <?php    }
                                            }
                                            ?>
                                            </select>
                                            <input type="hidden" id="no_cheque0" name="tipospago[0][no_cheque]" placeholder="N&uacute;mero de Cheque" value="" class="form-control m-t-sm hide" disabled="disabled" field-new-colum="false" />
                                            </td>
                                            <td class="textodos0" width="20%"><input type="text" id="textodos0" class="form-control" value="Total pagado" name="" readonly="readonly" style="border:0px;"></td>
                                            <td class="monto_pagar0" width="25%">
                                                <div class="input-group">
                                                    <span class="input-group-addon">$</span>
                                                    <input type="text" id="monto_a_pagar0" class="form-control" value="<?php echo $pago->monto?>" name="tipospago[0][monto]" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" ng-blur="CalcularMontoTotal($event)" data-rule-required="true" data-msg-required="" disabled="disbaled"/>
                                                </div>
                                                <input type="hidden" id="banco0" name="tipospago[0][banco]"  placeholder="Banco" value="" class="form-control m-t-sm hide" disabled="disabled" field-new-colum="false" disabled="disbaled"/>
                                            </td>
                                            <td width="5%"><button agrupador="transferir" class="btn btn-default btn-block eliminarPagoBtn hide" type="button"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Eliminar</span></button></td>
                                            <td width="5%"><button agrupador="transferir" class="btn btn-default btn-block agregarPagosBtn hide" type="button"><i class="fa fa-plus"></i><span class="hidden-xs hidden-sm hidden-md hidden-lg">&nbsp;Agregar</span></button></td>
                                            </tr>
                                                <?php }?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3"></td>
                                                    <td class="monto_pagar0" width="25%">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="text" id="total_a_pagar" class="form-control" value="<?php echo $transferir->monto?>" name="total_a_pagar" readonly="readonly" data-rule-equalto="#monto" disabled="disbaled"/>
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
                                <button type="button" id="guardarBtn" class="btn btn-primary btn-block {{disabledBtn}}" ng-bind-html="guardarBtn" ng-click="guardar($event)" >Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
 
                <input type="hidden" name="id" value="<?php echo!empty($transferir) ? $transferir->id : ""; ?>" />
                <input type="hidden" name="caja_id" value="<?php echo !empty($caja_id) ? $caja_id : ""; ?>" />
                <?php echo form_close(); ?>

            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
