<h5 style="font-size:14px">Datos de reclamo</h5>
<hr style="margin-top:10px!important;"> 
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label>Fecha de reclamo <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="fecha_reclamo" name="camporeclamo[fecha]" class="form-control" value="{{reclamoInfo.fecha}}" data-rule-required="true" disabled="disabled">
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Numero de caso <span required="" aria-required="true">*</span></label>
        <input  value="{{reclamoInfo.numero_caso}}" name="camporeclamo[numero_caso]" type="text" id="numero_caso" class="form-control" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
    </div>     
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Fecha de siniestro<span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="fecha_siniestro" name="camporeclamo[fecha_siniestro]" class="form-control" value="{{reclamoInfo.fecha_siniestro}}" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div> 
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Fecha de notificacion <span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
            <input type="input" id="fecha_notificacion" name="camporeclamo[fecha_notificacion]" class="form-control" value="{{reclamoInfo.fecha_notificacion}}" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>    
</div>
<div class="row" style="margin-top: 10px">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        <label>Reclamante</label>
        <select name="camporeclamo[reclamante]" id="reclamante" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
            <option value="">Seleccione</option>
            <option v-for="reclamante in reclamantes | orderBy 'nombre'" v-bind:value="reclamante.nombre" :selected="reclamante.nombre == reclamoInfo.reclamante" telefono="{{reclamante.telefono}}" correo="{{reclamante.correo}}">{{ reclamante.nombre }}
            </option>
            <option value="otros" :selected="reclamoInfo.reclamante == 'otros'">Otros</option>
        </select>
        <div id="reclamante_otros" style="display:none">
            <label>Otro: <span required="" aria-required="true">*</span></label>
            <input type="text" id="reclamante_otro" name="camporeclamo[reclamante_otro]" value="{{reclamoInfo.reclamante_otro}}" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>        
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        <label>Teléfono<span required="" aria-required="true">*</span></label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>   
            <input data-rule-required="true" type="text" name="camporeclamo[telefono]"  id="telefono" value="{{reclamoInfo.telefono}}" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label>Celular</label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>   
            <input type="text" value="{{reclamoInfo.celular}}" name="camporeclamo[celular]" class="form-control" id="celular" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label>Correo</label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-at"></i></span>   
            <input type="email" value="{{reclamoInfo.correo}}" name="camporeclamo[correo]" class="form-control" id="correo" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div>
    </div>
</div>
<div class="row" style="margin-top: 10px">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
        
        <label><?php if (isset($tipo_interes)) { if ($tipo_interes == 8 || $tipo_interes == 5){ echo "No. Certificado"; }else{ echo "Identificación"; } } ?></label>
        <div class="input-group">
            <input data-rule-required="true" type="text" id="reclamointeres" name="camporeclamo[no_certificado]" value="{{reclamoInfo.no_certificado}}" class="form-control" readonly="readonly">
            <span class="input-group-addon" style="cursor:pointer;" id="modalInteres"><i class="fa fa-binoculars fa-2" aria-hidden="true"></i></span>
            <input type="hidden" id="reclamoidinteres" name="camporeclamo[id_interes_asegurado]" value="{{reclamoInfo.id_interes_asegurado}}" >
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 plan">
        <br>
        <button type="button" class="btn btn-default btn-block" v-on:click='coberturasModal()' id="ver_coberturas" style="width:100%;" disabled="disabled">Coberturas</button>
    </div>   
    <div id="divintereses"></div> 
</div>

                        