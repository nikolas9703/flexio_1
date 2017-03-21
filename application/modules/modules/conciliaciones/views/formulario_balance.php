<?php
$info = !empty($info) ? $info : array();
//dd($info);
?>

<div class="row" style="margin-left: -15px;">

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="ibox border-bottom">
            <div class="ibox-title">
                <h5>Balance en banco</h5>
            </div>

            <div class="ibox-content" style="display:block;">
                <div v-if="acceso">
                    <div class="form-group has-success" style="margin-top: 20px;margin-bottom: 20px !important;">
                        <input type="text" name="campo[balance_banco]" class="form-control" placeholder="0.00" v-model="balances.balance_banco.monto | redondeo | signoDollar"  style="border: 2px solid #27AAE1;color:#27AAE1;font-weight:bold;text-align:center;" :disabled="!visible">
                    </div>
                    <p style="font-size: 10px;text-align:center;">(Haga clic para agregar el balance en banco desde {{campo.fecha_inicio}})</p>
                </div>
            </div>
        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="ibox border-bottom" v-show="balances.balance_flexio.detalle1">
            <div class="ibox-title">
                <h5>Balance verificado en Flexio</h5>
                <div class="ibox-tools">
                    <a @click="get_detalle2(balances.balance_flexio)"><i class="fa fa-info-circle"></i></a>
                </div>
            </div>
             <div class="ibox-content" style="display:block;">
                <div v-if="acceso">
                    <div class="form-group has-success" style="margin-top: 20px;margin-bottom: 20px !important;">
                        <input readonly="" type="text" name="campo[balance_flexio]" class="form-control" value="{{balance_verificado_flexio | redondeo | signoDollar}}" placeholder="0.00"  style="border: 2px solid #5BB85C;color:#5BB85C;font-weight:bold;text-align:center;">
                    </div>
                    <p style="font-size: 10px;text-align:center;">(Última transacción en Flexio fue el {{campo.fecha_fin}})</p>
                </div>
            </div>
        </div>

        <div class="ibox border-bottom" v-show="balances.balance_flexio.detalle2">
            <div class="ibox-title">
                <h5>Balance verificado en Flexio</h5>
                <div class="ibox-tools">
                    <label style="padding:2px 7px;text-align:center;font-weight:bold;border:#5BB85C solid 2px;color: #5BB85C;">{{balance_verificado_flexio | moneda}}</label>
                    <a @click="get_detalle1(balances.balance_flexio)"><i class="fa fa-times"></i></a>
                </div>
            </div>

            <div class="ibox-content" style="display:block;min-height: 126px;">
                <div v-if="acceso">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">Retiros ({{retiros_verificados_count}})</div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="text-align:right;font-weight:bold;">{{retiros_verificados_sum | moneda}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">Dep&oacute;sitos ({{depositos_verificados_count}})</div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="text-align:right;font-weight:bold;">{{depositos_verificados_sum | moneda}}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">

        <div class="ibox border-bottom" v-show="balances.diferencia.detalle1">
            <div class="ibox-title">
                <h5>Diferencia</h5>
                <div class="ibox-tools">
                    <a @click="get_detalle2(balances.diferencia)"><i class="fa fa-info-circle"></i></a>
                </div>
            </div>

            <div class="ibox-content" style="display:block;min-height: 134px;">
                <div v-if="acceso">
                    <div class="form-group has-danger" style="margin-top: 20px;margin-bottom: 20px !important;">
                        <input readonly="" type="text" name="campo[diferencia]" class="form-control" placeholder="0.00" value="{{diferencia | redondeo | signoDollar}}"  style="border: 2px solid #D9534E;color:#D9534E;font-weight:bold;text-align:center;">
                    </div>
                    <p style="font-size: 10px;text-align:center;">{{balances.diferencia.texto}}</p>
                </div>
            </div>
        </div>

        <div class="ibox border-bottom" v-show="balances.diferencia.detalle2">
            <div class="ibox-title">
                <h5>Diferencia</h5>
                <div class="ibox-tools">
                    <label style="padding:2px 7px;text-align:center;font-weight:bold;border:#D9534F solid 2px;color: #D9534F;">{{diferencia | moneda}}</label>
                    <a @click="get_detalle1(balances.diferencia)"><i class="fa fa-times"></i></a>
                </div>
            </div>

            <div class="ibox-content" style="display:block;min-height: 126px;">
                <div v-if="acceso">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="font-weight: bold;">No Verificado</div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="text-align: right"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">Retiros ({{retiros_no_verificados_count}})</div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="text-align:right;font-weight:bold;">{{retiros_no_verificados_sum | moneda}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">Dep&oacute;sitos ({{depositos_no_verificados_count}})</div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="text-align:right;font-weight:bold;">{{depositos_no_verificados_sum | moneda}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="font-weight:bold;">Verificado en Flexio</div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="text-align:right;font-weight:bold;">{{balance_verificado_flexio | moneda}}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
