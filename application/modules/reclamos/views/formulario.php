<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formReclamosCrear',
    'autocomplete' => 'off'
);
?>
<?php
if (!isset($campo)) {
    $campo = array();
}
?>
<style type="text/css">
    .sticky{ position: fixed !important; top: 0px; z-index: 100; background: #e7eaec;}
</style>
<div id="" class="" >

    <div class="tab-content" >
        <!-- AQUI VA EL FORM CON EL ACTION -->
        <div id="" class="tab-pane active col-lg-12 col-md-12">

            <div class="ibox" >                    
                <div class="tabs-container">
                    <ul class="nav nav-tabs tab-principal">
                        <li class="active"><a data-toggle="tab" href="#tab-1" style="color:#337ab7!important;" onclick="location.href = '#datosdepoliza'">Datos de póliza</a></li>
                        <li class=""><a data-toggle="tab" href="#divplan" style="color:#337ab7!important;" onclick="location.href = '#datosdereclamo'">Datos de reclamo</a></li>
                        <li class=""><a data-toggle="tab" href="#divintereses" style="color:#337ab7!important;" onclick="location.href = '#divintereses'">Interés asegurado</a></li>
                        <li class=""><a data-toggle="tab" href="#divvigencia" style="color:#337ab7!important;" onclick="location.href = '#detallereclamo'">Detalle de reclamo</a></li>
                        <li class=""><a data-toggle="tab" href="#divprima" style="color:#337ab7!important;" onclick="location.href = '#documentacionreclamo'">Documentación</a></li>
                        <li class=""><a data-toggle="tab" href="#participacion" style="color:#337ab7!important;" onclick="location.href = '#infopagoreclamo'">Información de pago</a></li>
                    </ul>
                </div>

                <div class="ibox-content" style="display: block;" id="datosdepoliza">                    
                    

                    <?php echo form_open_multipart(base_url('reclamos/guardar'), $formAttr); ?>

                    <div class="row">
                        <?php echo modules::run('reclamos/formulariopoliza', $campo); ?>                        
                    </div>

                    <!-- Variables Ocultas -->
                    <input type="hidden" id="id_poliza" name="camporeclamo[id_poliza]" value="{{polizaInfo.idpoliza}}">
                    <input type="hidden" name="camporeclamo[id_cliente]" value="{{polizaInfo.id_cliente}}">
                    <input type="hidden" name="campocoberturas" id="campocoberturas" value="">
                    <input type="hidden" name="campodeducciones" id="campodeducciones" value="">
                    <input type="hidden" id="campodeduccionsalud" value="{{valorSaludDed[0]}}">
                    <input type="hidden" name="camporeclamo[tipo_interes]" id="campotipointeres" value="">

                    <input type="hidden" name="campovalida[aseguradora_id]" id="aseguradora_id" value="{{polizaInfo.id_aseguradora}}">
                    
                    <input name="codigo_ramo"  type="hidden" v-model="codigoRamo" />
                    <input id="nombre_padre" type="hidden" v-model="nombrepadre" />

                    <input type="hidden" id="idReclamo" name="id_reclamo" value="">
                    <input type="hidden" id="ramo_id" name="camporeclamo[id_ramo]" value="">
                    <input type="hidden" name="camporeclamo[uuid]" id="camporeclamo[uuid]" value="">                    

                    <input type="hidden" name="detalleunico" id="detalleunico" value="<?php echo strtotime('now'); ?>">
                    <!-- Fin Variables Ocultas -->

                    <div class="row" id="formulariodatosreclamo">
                        <?php echo modules::run('reclamos/formulariodatosreclamo', $tipo_interes); ?>                
                    </div> 
                    <!-- Intereses de solicitud -->    
                    <div class="row" id="formulariointereses" style="margin-top: 20px">
                        <?php echo str_replace("</form>", "", modules::run('reclamos/formulariointereses', $tipo_interes)) ; ?>                       
                    </div> 
                    <!-- Detale Reclamo -->
                    <div class="row" style="margin-top: -111px" id="formulariodetallereclamo">
                        <?php echo modules::run('reclamos/formulariodetallereclamo', $campo); ?>                       
                    </div> 
                    <!-- Documentos -->
                    <div class="row" style="margin-top: 20px" id="formulariodocumentos">
                        <?php echo modules::run('reclamos/formulariodocumentos', $id_ramo); ?>
                        <div id="infopagoreclamo"></div>
                    </div> 

                    <!-- Pago -->
                    <div class="row" id="formulariopago">
                        <?php echo modules::run('reclamos/formulariopago', $campo); ?>
                    </div> 


                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
                        <label>Asignado a<span required="" aria-required="true">*</span></label>
                            <select name="camporeclamo[id_usuario]" class="form-control" id="asignado_a" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' ">
                                <option value="">Seleccione</option>
                                <option v-for="usuario in listadoUsuarios" v-bind:value="usuario.id" :selected="usuario.id == usuario_id">{{usuario.nombre + ' ' + usuario.apellido}}</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
                            <label>Estado del reclamo<span required="" aria-required="true">*</span></label>
                            <select  name="camporeclamo[estado]" class="form-control" id="estado" data-rule-required="true" :disabled="reclamoInfo.estado == 'Cerrado' || reclamoInfo.estado == 'Anulado' ">
                                <option value="">Seleccione</option>
                                <option v-for="estado in catalogoEstado" v-bind:value="estado.etiqueta" :selected="(estado.etiqueta == reclamoInfo.estado && vista == 'editar') || (estado.etiqueta == 'En analisis' && vista == 'crear') ">{{estado.etiqueta}}</option>
                            </select>
                        </div>  
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" >
                            <label>Fecha de Seguimiento</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                <input type="text" class="form-control" id="fecha_seguimiento" value="{{reclamoInfo.fecha_seguimiento}}" name="camporeclamo[fecha_seguimiento]">
                            </div>
                        </div>                      
                    </div>

                    <div class="row"> 
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <a href="<?php echo base_url('solicitudes/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> 
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 guardarsolicitud">
                            <input type="submit" name="camporeclamo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="camporeclamo[guardar]" >
                        </div>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>     
        
    </div>
</div>


<?php

echo Modal::config(array(
    "id" => "verModalIntereses",
    "size" => "lg"
))->html();

echo Modal::config(array(
    "id" => "opcionesModalIntereses",
    "size" => "sm"
))->html();


echo Modal::config(array(
    "id" => "AprobarSolicitud",
    "size" => "lg",
))->html();

echo Modal::config(array(
    "id" => "AnularSolicitud",
    "size" => "lg",
))->html();

echo Modal::config(array(
    "id" => "RechazarSolicitud",
    "size" => "lg",
))->html();
?>
