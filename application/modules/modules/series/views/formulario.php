

<div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" disabled="" class="form-control debito" value="{{detalle.adquisicion | currency ''}}">
        </div>
        <label class="label-success-text" style="border: #0076BE solid 1px;background-color: #0076BE;">Adquisici&oacute;n</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" disabled="" class="form-control debito" value="{{detalle.otros_costos | currency ''}}">
        </div>
        <label class="label-danger-text">Otros costos</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" disabled="" class="form-control debito" value="{{detalle.depreciacion | currency ''}}">
        </div>
        <label class="label-warning-text">Depreciaci&oacute;n</label>
    </div>

    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <label></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="text" disabled="" class="form-control debito" value="{{detalle.valor_actual | currency ''}}">
        </div>
        <label class="label-success-text" style="border: #5BC0DE solid 1px;background-color: #5BC0DE;">Valor actual</label>
    </div>

</div>

<div style="padding-top: 15px;clear: both;">

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <div style="padding-bottom: 10px;">
            <label><strong>Estado: </strong></label><label class="label" :class="detalle.estado == 'Disponible' ? 'label-successful' : 'label-warning'" style="float: right;padding: 5px;clear: both;font-size: 13px;" v-html="detalle.estado"></label>
        </div>
        <div style="padding-bottom: 10px;">
            <label><strong>Fecha de compra: </strong></label><label class="label label-info" style="float: right;padding: 5px;clear: both;font-size: 13px;" v-html="detalle.fecha_compra"></label><br>
        </div>
        <div style="padding-bottom: 10px;">
            <label><strong>Edad: </strong></label><label class="label label-info" style="float: right;padding: 5px;clear: both;font-size: 13px;" v-html="detalle.edad"></label><br>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
        <div style="padding-bottom: 10px;">
            <label><strong>&Uacute;ltimo movimiento: </strong></label>
        </div>
        <div style="padding-bottom: 10px;">
            <label style="font-weight: normal;">M&oacute;dulo: </label><label style="float: right;font-weight: normal;" v-html="detalle.um.modulo"></label><br>
        </div>
        <div style="padding-bottom: 10px;">
            <label style="font-weight: normal;">N&uacute;mero: </label><label style="float: right;font-weight: normal;" v-html="detalle.um.numero"></label><br>
        </div>
        <div style="padding-bottom: 10px;">
            <label style="font-weight: normal;">Nombre: </label><label style="float: right;font-weight: normal;" v-html="detalle.um.nombre"></label><br>
        </div>
        <div style="padding-bottom: 10px;">
            <label style="font-weight: normal;">Ubicaci&oacute;n: </label><label style="float: right;font-weight: normal;" v-html="detalle.um.ubicacion"></label><br>
        </div>
        <div style="padding-bottom: 10px;">
            <label style="font-weight: normal;">Fecha y hora: </label><label style="float: right;font-weight: normal;" v-html="detalle.um.fecha_hora"></label><br>
        </div>
    </div>

</div>
