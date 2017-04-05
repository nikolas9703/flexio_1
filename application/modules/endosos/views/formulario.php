
<div style="display: block;" id="datos_endosos">
    <h5 style="font-size:14px">Datos generales</h5>
    <hr style="margin-top:10px!important;"> 
    <input type="hidden" name="campos[uuid]" id="uuid_endoso" value="{{uuid_endoso}}">
    <input type="hidden" name="campos[id_endosos]" id="id_endosos" value="{{id_endoso}}">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <label>Tipo de endoso <span required="" aria-required="true">*</span></label>
            <select id="tipo_endoso" name="campos[tipo]" class="form-control" data-rule-required="true" @change="getTipoMotivo()" >
                <option value="">Seleccione</option>
                <option value="Activación" :selected=" 'Activación' == tipo_endoso">Activación</option>
                <option value="Cancelación" :selected=" 'Cancelación' == tipo_endoso">Cancelación</option>
                <option value="Regular" :selected=" 'Regular' == tipo_endoso">Regular</option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Motivo <span required="" aria-required="true">*</span></label>
            <select id="motivo_endoso" name="campos[motivo]" class="form-control" data-rule-required="true" :disabled="disabledMotivo" onchange="verModificaPrima()" >
                <option value="">Seleccione</option>
                <option v-for="mot in motivos_endosos" v-bind:value="mot.id" :selected="mot.id == id_motivo">{{mot.valor}}</option>
            </select>
        </div>  
        <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <label>Modifica Prima</label>
            <select id="modifica_prima_endoso" name="campos[modifica_prima]" class="form-control">
                <option value="">Seleccione</option>
                <option value="si" :selected=" 'si' == modifica_prima">Si</option>
                <option value="no" :selected=" 'no' == modifica_prima">No</option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <label>Fecha Efectividad</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                <input type="input" name="campos[fecha_efectividad]" id="fecha_afectacion" value="" readonly="readonly" class="form-control">
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Descripcion <span required="" aria-required="true">*</span></label>
            <textarea class="form-control" id="descrpcion_endoso" name="campos[descripcion]" rows="8" style="resize:vertical;" data-rule-required="true"><?php echo $valor_descripcion?></textarea>
        </div>
    </div>
</div>
<br><br>
<div style="display: block;" id="documentos_endosos" class="row">
    <h5 style="font-size:14px">Documentos entregados</h5>
    <hr style="margin-top:10px!important;"> 
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <label>Nombre del documento</label>
        <div class='file_upload_endoso' id='fendoso1'>
            <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" />
            <input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
        </div>
        <br><br>
        <div id='file_tools_endoso' style="width: 90px!important; float: left;">
            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_endoso"><i class="fa fa-plus"></i>
            </button>
            <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_endoso"><i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
</div>

<br><br>
<div class="row">
    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <label for="">Estado</label>
        <select class="form-control" id="estado_endosos" name="campos[estado]">
            <option v-for="est in estadosEndosos" v-bind:value="est.valor" :selected="est.valor == estado_endoso">{{est.valor}}</option>
        </select>
    </div>
    <div class="col-xs-0 col-sm-3 col-md-6 col-lg-6">&nbsp;</div>

    <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <a href="<?php echo base_url('endosos/listar'); ?>" class="btn btn-default btn-block" id="cancelar_endoso" style="margin-top:20px;">Cancelar</a> 
    </div>
    <div class="<col-xs-12 col-sm-3 col-md-2 col-lg-2 guardarsolicitud">
        <input type="submit" id="guardar_endoso" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit" style="margin-top:20px;">
    </div>
</div>
