<h5 style="font-size:14px">Datos de póliza</h5>
<hr style="margin-top:10px!important;"> 
<div class="row">
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <label>Numero de póliza <span required="" aria-required="true">*</span></label>
        <input type="text" value="{{polizaInfo.numeropoliza}}" class="form-control" id="numero_poliza" data-rule-required="true" disabled>
    </div>
    <!--seccion de identificacion del cliente-->      
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label for="">Vigencia<span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="poliza_vigencia_desde" disabled="disabled" class="form-control" value="{{polizaInfo.vigencia_desde}}" data-rule-required="true">
            <span class="input-group-addon">a</span>
            <input type="input" id="poliza_vigencia_hasta" class="form-control" value="{{polizaInfo.vigencia_hasta}}" data-rule-required="true" disabled="disabled">
        </div>
    </div> 
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Cliente <span required="" aria-required="true">*</span></label>
        <input  value="{{polizaInfo.nombre_cliente}}" type="text" id="nombre_cliente" class="form-control" disabled>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Aseguradora <span required="" aria-required="true">*</span></label>
        <input  value="{{polizaInfo.nombre_aseguradora}}" type="text" id="nombre_aseguradora" class="form-control" disabled>
    </div>
</div>
<div class="row" id="poliza_acre">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        <label>Acreedor Hipotecario</label>
        <input type="text" id="acreedor_hipotecario" value="{{polizaInfo.acreedor_hipotecario}}" class="form-control" disabled>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        <label>% Asignado al acreedor</label>
        <input type="text"  id="porcentaje_acreedor" value="{{polizaInfo.porcentaje_acreedor}}" class="form-control" disabled>
    </div>
    <div id="datosdereclamo"></div>
</div>

                        