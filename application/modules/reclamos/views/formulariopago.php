<h5 style="font-size:14px">Información de pago</h5>
<hr style="margin-top:10px!important;"> 
<div class="row" >
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label>Total a reclamar<span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>    
            <input type="input" id="total_reclamar" name="camporeclamo[total_reclamar]" class="form-control" value="{{reclamoInfo.total_reclamar}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Pago asegurado <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>    
            <input type="input" id="pago_asegurado" name="camporeclamo[pago_asegurado]" class="form-control" value="{{reclamoInfo.pago_asegurado}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>     
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label>Pago a deducible<span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>    
            <input type="input" id="pago_deducible" name="camporeclamo[pago_deducible]" class="form-control" value="{{reclamoInfo.pago_deducible}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div> 
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Gastos no cubiertos <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-usd"></i></span>    
            <input type="input" id="gastos_no_cubiertos" name="camporeclamo[gastos_no_cubiertos]" class="form-control" value="{{reclamoInfo.gastos_no_cubiertos}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>    
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        <label>No. de Cheque</label>
        <input type="text" id="numero_cheque" name="camporeclamo[numero_cheque]" value="{{reclamoInfo.numero_cheque}}" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        <label>Fecha de Cheque<span required="" aria-required="true" id="spanfechacheque" style="display: none">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="fecha_cheque" name="camporeclamo[fecha_cheque]" class="form-control" value="{{reclamoInfo.fecha_cheque}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>
</div>                        