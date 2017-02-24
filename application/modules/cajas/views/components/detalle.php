
<template id="detalle_template">

    <div class="row">
           <input type="hidden" name="campo[desde_caja_id]" value="{{detalle.caja_id}}">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>{{detalle.label_acuenta}}<span class="required">*</span></label>

 <select name="campo[cuenta_id]" id="cuenta_id" data-rule-required="true" aria-required="true"    :disabled="config.cuentaDisabled"  v-model="detalle.cuenta_id" v-select2="detalle.cuenta_id"  :config="config.select2"  >
                <option value="">Seleccione</option>
                <option :value="cuenta.id" v-for="cuenta in getTransferenciaDesde">{{cuenta.nombre}}</option>
            </select>


                            </div>



                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Monto <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" id="monto"  class="form-control" value="" name="campo[monto]" data-inputmask="'mask':'9{0,13}[.99]','greedy':false" data-rule-required="true" data-msg-required=""  v-model="detalle.monto" />
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                
                                            <label for="fecha_desde">Fecha <span required="" aria-required="true">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                                        <input type="text" name="campo[fecha]" class="form-control"  id="fecha" data-rule-required="true" v-model="detalle.fecha_desde" v-datepicker2="detalle.fecha_desde" :config="config.fecha_desde" :disabled="config.disableDetalle">
                                    </div>
                                    <label id="fecha_desde-error" class="error" for="fecha_desde"></label>

                            </div>






    </div>


    <div class="row">
         <div class="col-lg-12">
                                 <!-- <div id="vue-correo-clientes">-->
                                    <div class="col-lg-12">
                                        <table class="table table-noline">

                                            <tbody>
                                            <tr v-for="monto in detalle.metodos_pagos">
                                                 <td width="22%" style="vertical-align: middle;"   >
                                                     <label for="fecha_desde">Método de pago:</label>
                                                </td>
                                                <td width="22%" >

                                                     <select name="tipospago[{{$index}}][tipo_pago_id]" id="metodo{{$index}}" class="form-control"  data-rule-required="true" aria-required="true" >
                                                        <option value="">Seleccione</option>
                                                        <option :value="metodo.id" v-for="metodo in catalogos.metodos_pagos">{{metodo.valor}}</option>
                                                    </select>


                                                </td>
                                                  <td width="22%"  style="vertical-align: middle;">
                                                     <label for="fecha_desde">Total Pagado:</label>
                                                </td>
                                                <td width="22%" >
 <div class="input-group">
                                                        <span class="input-group-addon">$</span>
                                                    <!--<input type="hidden" name="montos[{{$index}}][monto]" value="{{monto.monto}}">-->
                                                    <input type="input-left-addon" class="form-control" name="tipospago[{{$index}}][monto]" id="monto{{$index}}" v-model="monto.monto"  data-rule-required="true">
                                                    </div>

                                                </td>
                                                <td width="10%">
                                                    <button type="button" class="btn btn-default btn-block" v-show="$index === 0" v-on:click="addFilasCorreo($event)" data-rule-required="true" agrupador="monto" aria-required="true"><i class="fa fa-plus"></i></button>
                                                    <button type="button" v-show="$index !== 0" class="btn btn-default btn-block" v-on:click="monto.length === 1 ?'':deleteFilasCorreo($index)" data-rule-required="true" agrupador="monto" aria-required="true" ><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            </tbody>

                                             <tfoot>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td width="22%" >
                                                         <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="text" id="monto"  class="form-control"   value="{{total_monto}}" name="campo[monto_total]"   data-rule-required="true" data-msg-required="" :disabled="config.montoTotalDisabled"   />
                                                        </div>
                                                         <label class="label m-t-xs btn-primary p-xs col-xs-12 col-sm-12 col-md-12 col-lg-12">Total</label>
                                                    </td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>
                                <!-- </div>-->

                            </div>
                        </div>



</template>
