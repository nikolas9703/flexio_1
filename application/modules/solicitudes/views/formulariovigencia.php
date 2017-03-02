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

<div class="row" id="vigencia_vida_individual" style="display: none">
    <div id="campos_vida_acreedor_crear">
        <div class="row" id="divacreedores">
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                <label class="nombre_doc_titulo" id="nombre_acre_titulo">Acreedor</label>
            </div>    
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_cesion">% Cesión</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_monto">Monto</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_inicio">Fecha Inicio</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_fin">Fecha Fin</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" ></label>
            </div>        
        </div>
        <div style="margin-bottom: 25px;" class="">
            <div class="file_tools_acreedores_adicionales row" id="a1">
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                    <input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control">
                </div>    
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">%</span> 
                        <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">$</span> 
                        <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                        <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                        <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" style="float: left; width: 40px; margin-right:5px;" onclick="agregaracre()"><i class="fa fa-plus"></i>
                    </button>
                    <button type="button" style="float: left; width: 40px; margin-top:0px!important; display: none" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i>
                    </button>
                </div>
            </div> 
            <div id="agrega_acre"></div>           
        </div>
    </div> 
    <div id="campos_vida_acreedor_editar">
        <div class="row" id="divacreedores">
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                <label class="nombre_doc_titulo" id="nombre_acre_titulo">Acreedor</label>
            </div>    
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_cesion">% Cesión</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_monto">Monto</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_inicio">Fecha Inicio</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" id="nombre_acre_fin">Fecha Fin</label>
            </div> 
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                <label class="nombre_doc_titulo" ></label>
            </div>        
        </div>

        <div style="margin-bottom: 25px;" class="">
            <div class="file_tools_acreedores_adicionales row" v-for="find in acreedores" track-by="$index" id="a{{$index + 2}}">
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                    <input type="text" name="campoacreedores[]" id="acreedor_{{$index + 2}}" class="form-control" value="{{find.acreedor}}">
                </div>    
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">%</span> 
                        <input type="text" name="campoacreedores_por[]" id="porcentajecesion_{{$index + 2}}" class="form-control porcentaje_cesion_acreedor" value="{{find.porcentaje_cesion}}">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">$</span> 
                        <input type="text" name="campoacreedores_mon[]" id="montocesion_{{$index + 2}}" class="form-control monto_cesion_acreedor" value="{{find.monto_cesion}}">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                        <input type="text" name="campoacreedores_ini[]" id="fechainicio_{{$index + 2}}" class="form-control fechas_acreedores_inicio" value="{{find.fecha_inicio}}">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                        <input type="text" name="campoacreedores_fin[]" id="fechafin_{{$index + 2}}" class="form-control fechas_acreedores_fin" value="{{find.fecha_fin}}">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <!--<button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i>
                    </button>-->
                    <button type="button" style="float: left; width: 40px; margin-top:0px!important; display: block !important" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaacreedor({{$index+2}})"><i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="file_tools_acreedores_adicionales row" id="a1">
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px">
                    <input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control">
                </div>    
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">%</span> 
                        <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon">$</span> 
                        <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                        <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio">
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                        <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                    <button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i>
                    </button>
                    <button type="button" style="float: left; width: 40px; margin-top:0px!important;" id="del_acre" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>

            <div id="agrega_acre"></div> 
        </div>

    </div>   
</div>
