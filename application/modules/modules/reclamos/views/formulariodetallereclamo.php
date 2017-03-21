<h5 style="font-size:14px">Detalle de reclamo</h5>
<hr style="margin-top:10px!important;"> 
<div id="detallereclamo_todos">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Ajustador</label>
            <select name="camporeclamo[ajustador]" id="ajustador" class="form-control" onchange="cambiaAjustador(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  || permiso_editar == '0' ">
                <option value="">Seleccione</option>
                <option v-for="ajustadores in listaAjustadores | orderBy 'nombre'" v-bind:value="ajustadores.id" :selected="ajustadores.id == reclamoInfo.ajustador">{{ ajustadores.nombre }}
                </option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <label>Contacto</label>
            <select name="camporeclamo[contacto]" id="contacto" class="form-control" onchange="cambiaContacto(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="contacto in listaContactos | orderBy 'nombre'" v-bind:value="contacto.id" :selected="contacto.id == reclamoInfo.contacto">{{ contacto.nombre }}
                </option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Teléfono</label>
            <input type="text" name="camporeclamo[telefonodetalle]"  id="telefonodetalle" value="{{reclamoInfo.telefonodetalle}}" class="form-control" readonly="readonly">
        </div>    
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
            <label>Descripción de Siniestro</label>
            <textarea name="camporeclamo[descripcionsiniestro]" rows="5" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">{{reclamoInfo.descripcionsiniestro}}</textarea>
        </div> 
        <div id="documentacionreclamo"></div>  
    </div>
</div>

<div id="detallereclamo_vehiculo">
    <div class="row" >
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Causa <span required="" aria-required="true">*</span></label>
            <select name="camporeclamo[causa]" id="causa" class="form-control" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="causas in listaCausa | orderBy 'orden'" v-bind:value="causas.id" :selected="causas.id == reclamoInfo.causa">{{ causas.etiqueta }}
                </option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Ajustador</label>
            <select name="camporeclamo[ajustador]" id="ajustador" class="form-control" onchange="cambiaAjustador(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  || permiso_editar == 0 ">
                <option value="">Seleccione</option>
                <option v-for="ajustadores in listaAjustadores | orderBy 'nombre'" v-bind:value="ajustadores.id" :selected="ajustadores.id == reclamoInfo.ajustador">{{ ajustadores.nombre }}
                </option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <label>Contacto</label>
            <select name="camporeclamo[contacto]" id="contacto" class="form-control" onchange="cambiaContacto(this.value)" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="contacto in listaContactos | orderBy 'nombre'" v-bind:value="contacto.id" :selected="contacto.id == reclamoInfo.contacto">{{ contacto.nombre }}
                </option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Teléfono</label>
            <input type="text" name="camporeclamo[telefonodetalle]"  id="telefonodetalle" value="{{reclamoInfo.telefonodetalle}}" class="form-control" readonly="readonly">
        </div>    
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Taller</label>
            <input type="text" name="camporeclamo[taller]"  id="taller" value="{{reclamoInfo.taller}}" class="form-control" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
        </div> 
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Fecha del juicio</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                <input type="input" id="fecha_juicio" name="camporeclamo[fecha_juicio]" class="form-control" value="{{reclamoInfo.fecha_juicio}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
            </div>
        </div> 
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" >
            <label>Descripción de Accidente</label>
            <textarea name="camporeclamo[descripcionsiniestro]" class="form-control" rows="5" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">{{reclamoInfo.descripcionsiniestro}}</textarea>
        </div>         
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <br>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="61" :checked="reclamoInfoAcc.indexOf(61) >= 0"> Daño auto asegurado</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="62" :checked="reclamoInfoAcc.indexOf(62) >= 0"> Daño terceros</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="63" :checked="reclamoInfoAcc.indexOf(63) >= 0"> Robo</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="64" :checked="reclamoInfoAcc.indexOf(64) >= 0"> Comprensivo</div>
                <div class="row"><input type="checkbox"  name="campoaccidente[]" value="65" :checked="reclamoInfoAcc.indexOf(65) >= 0"> Pérdida total</div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
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

<div id="detallereclamo_salud">
    <input type="hidden" id="id_detalle_salud" value="0">
    <input type="hidden" id="id_reclamo_salud" value="0">
    <div class="row" >
        <input type="hidden" value="<?php echo(strtotime('now')); ?>" id="detalle_unico" name="camporeclamo_salud[detalle_unico]">
        <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" >
            <label>Tipo <span required="" aria-required="true">*</span></label>
            <select name="camporeclamo_salud[tipo_salud]" id="tipo_salud" class="form-control" :disabled="reclamoInfo.tipo_salud == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
                <option value="">Seleccione</option>
                <option v-for="tipo in listaSalud | orderBy 'orden'" v-bind:value="tipo.id" :selected="tipo.id == reclamoInfo.tipo_salud">{{ tipo.etiqueta }}
                </option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3" >
            <label>Clinica / Hospital</label>
            <input type="text" name="camporeclamo_salud[hospital]"  id="hospital" value="{{reclamoInfo.hospital}}" class="form-control">
        </div> 
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Especialidad</label>
            <input type="text" name="camporeclamo_salud[especialidad_salud]"  id="especialidad_salud" value="{{reclamoInfo.especialidad_salud}}" class="form-control">
        </div>         
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" >
            <label>Doctor</label>
            <input type="text" name="camporeclamo_salud[doctor]"  id="doctor" value="{{reclamoInfo.doctor}}" class="form-control">
        </div>  
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" >
            <br>
            <button type="button" class="btn btn-default btn-block limpiar_detalle_salud" style="float: left; width: 40px; margin-top:3px;"><i class="fa fa-times"></i>
            </button>
        </div>  
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5" >
            <label>Detalle</label>
            <input name="camporeclamo_salud[detalle_salud]" id="detalle_salud" type="text" class="form-control" value="{{reclamoInfo.detalle_salud}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0' ">
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <label for="">Fecha del siniestro</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>    
                <input type="text" id="fecha_salud" name="camporeclamo_salud[fecha_salud]" class="form-control" value="{{reclamoInfo.fecha_salud}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
            </div>
        </div> 
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3" >
            <label>Monto</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-usd"></i></span>    
                <input type="text" id="monto" name="camporeclamo_salud[monto_salud]" class="form-control" value="{{reclamoInfo.monto_salud}}" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' || permiso_editar == '0'  ">
            </div>
        </div> 
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" >
            <br>
            <button type="button" class="btn btn-default btn-block agregar_detalle_salud" style="float: left; width: 40px; margin-top:3px; "><i class="fa fa-plus"></i>
                    </button>
        </div>  
          
    </div>
    <div class="tabladetalle_salud">
        <!-- JQGRID -->
        <?php echo modules::run('reclamos/ocultotablasalud'); ?>
        <!-- /JQGRID -->
    </div>  
    <div id="documentacionreclamo"></div>  
</div>                       