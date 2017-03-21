<style type="text/css">
    .label_espacio{
        margin-left: 5px!important;
    }
    .columnasnombres{
        width: 50%;
        text-align: left;
    }

</style>
<?php
if (isset($campos['participacion']) && ($campos['participacion'] == 0)) {

    $disabled = "disabled=''";
} else {
    $disabled = "";
}
?>
<h5 style="font-size:14px">Distribuci&oacute;n de participaci&oacute;n</h5>
<hr style="margin-top:10px!important;">

<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="margin-left: -12px;">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
            <label>Agente</label>    
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>% Participaci&oacute;n</label>    
        </div>
    </div>

    <div id="editarParticipacion" class="hidden">
        <div v-for="par in participacion"  track-by="$index" id="total_agentes_participantes" class="total_agentes_participantes col-xs-10 col-sm-10 col-md-10 col-lg-10">
            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pie-chart"></i></span>
                    <select  name="agentes[]" class="form-control id_agentes" id="agentes_{{$index}}" @change="porcentajeAgentes($index)" onchange="verificaagente();">
                        <option value="">Seleccione</option>
                        <option v-for="agtList in agentesList" v-bind:value="agtList.id" :selected="agtList.id == par.agente">{{{agtList.nombre}}}</option>
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-4">
                <input type="text" name="campoparticipacio[porcentaje][{{$index}}]"  class="form-control" id="agentes_participacion_{{$index}}" @keyup="valorporcentajeAgentes($index)" value="{{par.porcentaje_participacion != '' ? par.porcentaje_participacion : ''}}"  >
            </div>

            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-2" style="margin-bottom: 5px;">
                <button id="removerAgente" type="button" class="btn btn-default btn-block" @click="removeAgenteEditar(par)"><i class="fa fa-trash"></i></button>
            </div> 
        </div>
        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-2">
            <button type="button" class="btn btn-default btn-block" @click="addAgenteEditar"><i class="fa fa-plus"></i></button>
        </div>
    </div>

    <div id="crearParticipacion" >
        <div v-for="agt in agentesArray" track-by="$index" id="total_agentes_participantes" class="total_agentes_participantes col-xs-10 col-sm-10 col-md-10 col-lg-10">
            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-pie-chart"></i></span>
                    <select  name="agentes[]" class="form-control id_agentes" id="agentes_{{$index}}" @change="porcentajeAgentes($index)" onchange="verificaagente();">
                        <option value="">Seleccione</option>
                        <option v-for="agtList in agentesList" v-bind:value="agtList.id">{{{agtList.nombre}}}</option>
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-4" <?php echo $disabled; ?> >
                <input type="number" name="campoparticipacion[porcentaje][{{$index}}]"  class="form-control" id="agentes_participacion_{{$index}}" @keyup="valorporcentajeAgentes($index)"  <?php echo $disabled; ?> >

            </div>
            <div class="col-xs-12 col-sm-4 col-md-6 col-lg-2" style="margin-bottom: 5px;">
                <button id="removerAgente" type="button" class="btn btn-default btn-block" @click="removeAgente(agt)"><i class="fa fa-trash"></i></button>
            </div>                                                
        </div>
        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-2">
            <button type="button" class="btn btn-default btn-block" @click="addAgente"><i class="fa fa-plus"></i></button>
        </div>
    </div>

    <input type="hidden" id="cantidad" name="campoparticipacion[cantidad]">
    <input type="hidden" id="agente" name="campoparticipacion[id_agente]">
    <input type="hidden" id="porcentaje" name="campoparticipacion[porcentajes]">
	<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 agentePrincipal ">
		<div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
			<div class="input-group">	
				<span class="input-group-addon"><i class="fa fa-pie-chart"></i></span>
				<select id='nombreAgentePrincipal' class="form-control" disabled>
				</select>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4 col-md-6 col-lg-4">
			 <input type="number" class="form-control" id="porcAgentePrincipal" disabled>  
		</div><br>
    </div>
    <div class="Totales col-xs-10 col-sm-10 col-md-10 col-lg-10" style=' margin-top: 5px !important;'>
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-6">
            <label>Totales</label>    
        </div>
        <div class="col-xs-12 col-sm-4 col-md-6 col-lg-4">
            <input type="number" title="La suma de los porcentajes debe ser menor a 100." name="campoparticipacion[total]" class="form-control" v-model="participacionTotal" min="0" max="100" id="participacionTotal" readonly="readonly"/>
        </div>
    </div>
</div>
<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="margin-left: -12px;">
    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <label>Observaciones de la solicitud</label>
        <textarea name="campo[observaciones]" class="form-control" id="observaciones_solicitudes"></textarea>
        <label for="campo[observaciones]" generated="true" class="error"></label>
    </div>
</div>
