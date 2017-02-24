<h5 style="font-size:14px">Vigencia y detalle de solicitud</h5>
<hr style="margin-top:10px!important;">
<div class="row">
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label for="">Vigencia<span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="vigencia_desde" name="campovigencia[vigencia_desde]" readonly="readonly" class="form-control" value="" data-rule-required="true">
            <span class="input-group-addon">a</span>
            <input type="input" id="vigencia_hasta" name="campovigencia[vigencia_hasta]" readonly="readonly" class="form-control" value="" data-rule-required="true">
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 ">
        <label>Suma Asegurada <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon">$</span>
            <input type="input-left-addon" name="campovigencia[suma_asegurada]" value="{{vigencia != false ? vigencia.suma_asegurada : ''}}" data-rule-required="true" class="form-control"  id="suma_asegurada">
        </div>                                
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 plan">
        <label>Pagador  <span required="" aria-required="true">*</span></label>
        <select  name="campovigencia[tipo_pagador]" class="form-control" id="pagador" data-rule-required="true" @change="opcionPagador();">
            <option value="">Seleccione</option>
            <option v-for="pag in catalogoPagador" v-bind:value="pag.valor" :selected="pag.valor == vigencia.tipo_pagador">{{pag.etiqueta}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 plan" id="divpagadornombre" style="display:none">
        <label>Nombre  <span required="" aria-required="true">*</span></label>
        <div id="divpgnombre"><input type="text" name="campovigencia[pagadornombre]" id="campopagador" value="{{vigencia != false ? vigencia.pagador : ''}}" class="form-control"></div>
        <div id="divselpagador"><select  name="campovigencia[selpagadornombre]" id="selpagadornombre" class="form-control" >
            <option value="">Seleccione</option>
            <option v-for="interaso in InteresesAsociados" v-bind:value="interaso.nombrePersona" :selected="interaso.numero == vigencia.pagador">{{interaso.nombrePersona}}</option>
        </select></div>    
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2 plan">
        
        <label>Póliza declarativa</label>
        <div class="switch">
        <div id="divprima"></div> <input type="checkbox" class="js-switch" name='campovigencia[poliza_declarativa]' id='polizaDeclarativa' :checked="vigencia.poliza_declarativa == 'si' "/>
    </div>   
       
 </div> 
 
</div> 
