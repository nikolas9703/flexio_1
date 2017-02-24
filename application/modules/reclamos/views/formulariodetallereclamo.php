<h5 style="font-size:14px">Detalle de reclamo</h5>
<hr style="margin-top:10px!important;"> 
<div id="detallereclamo_todos">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Ajustador</label>
            <select name="camporeclamo[ajustador]" id="ajustador" class="form-control" onchange="cambiaAjustador(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  || permiso_editar == '0' ">
                <option value="">Seleccione</option>
                <option v-for="ajustadores in listaAjustadores | orderBy 'nombre'" v-bind:value="ajustadores.id" :selected="ajustadores.id == reclamoInfo.ajustador">{{ ajustadores.nombre }}
                </option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <label>Contacto</label>
            <select name="camporeclamo[contacto]" id="contacto" class="form-control" onchange="cambiaContacto(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="contacto in listaContactos | orderBy 'nombre'" v-bind:value="contacto.id" :selected="contacto.id == reclamoInfo.contacto">{{ contacto.nombre }}
                </option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Teléfono</label>
            <input type="text" name="camporeclamo[telefonodetalle]"  id="telefonodetalle" value="{{reclamoInfo.telefonodetalle}}" class="form-control" readonly="readonly">
        </div>    
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <label>Descripción de Siniestro</label>
            <textarea name="camporeclamo[descripcionsiniestro]" rows="5" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">{{reclamoInfo.descripcionsiniestro}}</textarea>
        </div> 
        <div id="documentacionreclamo"></div>  
    </div>
</div>

<div id="detallereclamo_vehiculo">
    <div class="row" >
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Causa <span required="" aria-required="true">*</span></label>
            <select name="camporeclamo[causa]" id="causa" class="form-control" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="causas in listaCausa | orderBy 'orden'" v-bind:value="causas.id" :selected="causas.id == reclamoInfo.causa">{{ causas.etiqueta }}
                </option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Ajustador</label>
            <select name="camporeclamo[ajustador]" id="ajustador" class="form-control" onchange="cambiaAjustador(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  || permiso_editar == 0 ">
                <option value="">Seleccione</option>
                <option v-for="ajustadores in listaAjustadores | orderBy 'nombre'" v-bind:value="ajustadores.id" :selected="ajustadores.id == reclamoInfo.ajustador">{{ ajustadores.nombre }}
                </option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <label>Contacto</label>
            <select name="camporeclamo[contacto]" id="contacto" class="form-control" onchange="cambiaContacto(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="contacto in listaContactos | orderBy 'nombre'" v-bind:value="contacto.id" :selected="contacto.id == reclamoInfo.contacto">{{ contacto.nombre }}
                </option>
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Teléfono</label>
            <input type="text" name="camporeclamo[telefonodetalle]"  id="telefonodetalle" value="{{reclamoInfo.telefonodetalle}}" class="form-control" readonly="readonly">
        </div>    
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Taller</label>
            <input type="text" name="camporeclamo[taller]"  id="taller" value="{{reclamoInfo.taller}}" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div> 
        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Fecha del juicio</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                <input type="input" id="fecha_juicio" name="camporeclamo[fecha_juicio]" class="form-control" value="{{reclamoInfo.fecha_juicio}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
            </div>
        </div> 
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" >
            <label>Descripción de Accidente</label>
            <textarea name="camporeclamo[descripcionsiniestro]" class="form-control" rows="5" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">{{reclamoInfo.descripcionsiniestro}}</textarea>
        </div>         
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <br>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="61" :checked="reclamoInfoAcc.indexOf(61) >= 0"> Daño auto asegurado</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="62" :checked="reclamoInfoAcc.indexOf(62) >= 0"> Daño terceros</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="63" :checked="reclamoInfoAcc.indexOf(63) >= 0"> Robo</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="64" :checked="reclamoInfoAcc.indexOf(64) >= 0"> Comprensivo</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="65" :checked="reclamoInfoAcc.indexOf(65) >= 0"> Pérdida total</div>
            </div>
            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <br>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="66" :checked="reclamoInfoAcc.indexOf(66) >= 0"> Solicita asistencia legal</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="67" :checked="reclamoInfoAcc.indexOf(67) >= 0" > Uso de FUD</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="68" :checked="reclamoInfoAcc.indexOf(68) >= 0"> Lesionados</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="69" :checked="reclamoInfoAcc.indexOf(69) >= 0"> Alquiler de autos</div>
            </div>
            <!--<div class="" v-for="accidente in listaAccidente">
                <input type="checkbox"  name="campoaccidente[]" value="{{accidente.id}}"> {{accidente.etiqueta}}
            </div> -->           
        </div>      
        <div id="documentacionreclamo"></div>    
    </div> 
</div>                        