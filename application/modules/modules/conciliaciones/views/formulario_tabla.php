<?php
$info = !empty($info) ? $info : array();
//dd($info);
?>

<div>

    <table id="transaccionesTable" class="table table-noline tabla-dinamica table-striped">
        <thead>
            <tr>
                <th style="width: 20%;">
                    No. Transacci&oacute;n
                </th>
                <th style="width: 10%;">
                    Fecha
                </th>
                <th style="width: 35%;">
                    Transacci&oacute;n
                </th>
                <th style="width: 15%;">
                    Monto
                </th>
                <th style="width: 20%;">
                    Balance verificado
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="transaccion in transacciones" track-by="$index">
                <td>{{{transaccion.numero}}}</td>
                <td>{{transaccion.fecha}}</td>
                <td>{{{transaccion.transaccion}}}</td>
                <td v-bind:style="{color:transaccion.color}" style="text-align: right;">{{transaccion.monto | moneda}}</td>
                <td style="text-align: right;">
                    <label style="margin-right: 20px;;padding:2px 7px;text-align:center;font-weight:bold;border:#5BB85C solid 2px;color: #5BB85C;" v-show="transaccion.balance_verificado.checked">{{transaccion.balance_verificado.monto | moneda}}</label>

                    <input type="hidden" name="transacciones[{{$index}}][balance_verificado]" value="{{transaccion.balance_verificado.monto | redondeo}}">
                    <input type="hidden" name="transacciones[{{$index}}][order]" value="{{transaccion.balance_verificado.order}}">

                    <input type="checkbox" name="transacciones[{{$index}}][transaccion_id]" value="{{transaccion.id}}" class="icheck" style="float: right;"  v-model="transaccion.balance_verificado.checked" v-on:change="verificar_monto(transaccion)" :disabled="!visible">
                </td>
            </tr>
        </tbody>
    </table>

</div>

<br><br>
<div class="row">

    <div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>

    <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
        <a href="<?php echo base_url("conciliaciones/listar")?>"  class="btn btn-default btn-block">Cancelar</a>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
        <input type="submit"  class="btn btn-primary btn-block" value="Guardar" :disabled="!visible">
    </div>

</div>


<style type="text/css">
    table#transaccionesTable thead th {
        background-color: #0076BE;
        color: white;
        border: 1px solid white !important;
        font-weight: bold;
        padding-left: 7px !important;
    }
</style>
