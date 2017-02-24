<?php
$info = !empty($info) ? $info : array();
//dd($info);
?>


        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <label for="">Cuenta de banco</label>

                <select name="campo[cuenta_id]" class="form-control select2" id="cuenta_id" v-model="campo.cuenta_id">
                    <option v-for="cuenta_banco in campo.cuentas_bancos" :value="cuenta_banco.cuenta_id" v-text="cuenta_banco.cuenta.nombre"></option>
                </select>

            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <label for="">Rango de fecha</label>

                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <input type="text" name="campo[fecha_inicio]" id="fecha_inicio" class="form-control" v-model="campo.fecha_inicio">
                  <span class="input-group-addon">a</span>
                  <input type="text" class="form-control" name="campo[fecha_fin]" id="fecha_fin" v-model="campo.fecha_fin">
                </div>

            </div>

            <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10"></div>

            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                <input type="submit" id="guardarBtn"  class="btn btn-primary btn-block" value="Actualizar" v-on:click="actualizar($event)" :disabled="disabled_actualizar" v-if="visible"/>
            </div>

        </div>

        <div class="modal fade" id="opcionesModal"  tabindex="-1" role="dialog" aria-labelledby="opcionesModal" aria-hidden="true">
             <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">Balance de banco</h4>
                    </div>
                    <div class="modal-body">

                        <p style="font-size: 10px;text-align:center;">Ingrese balance en el banco al fin de día del {{campo.fecha_inicio}}</p>

                        <div class="form-group has-success" style="margin-top: 20px;margin-bottom: 20px !important;">
                            <input type="text" class="form-control" placeholder="0.00" v-model="balances.balance_banco.monto | redondeo"  style="border: 2px solid #27AAE1;color:#27AAE1;font-weight:bold;text-align:center;">
                        </div>

                        <div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <input type="button"  class="btn btn-default btn-block" data-dismiss="modal" value="Cancelar"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <input type="button"  class="btn btn-primary btn-block" data-dismiss="modal" value="Guardar"/>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>
