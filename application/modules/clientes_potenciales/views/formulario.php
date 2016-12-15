 <div class="ibox-content m-b-sm" style="display: block; border:0px">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Nombre de cliente potencial <span required="" aria-required="true">*</span></label>
            <input type="text" name="campo[nombre]" value="<?php echo isset($campos->nombre)?$campos->nombre:'';?>" class="form-control" data-rule-required="true" id="campo[nombre]" aria-required="true">
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 TomadeContacto ">
            <label>Toma de Contacto </label>

            <select name="campo[id_toma_contacto]" class="form-control select2" id="id_toma_contacto">
                <option value="">Seleccione</option>
                <?php foreach($toma_contactos as $toma_contacto):?>
                <option value="<?php echo $toma_contacto->id?>" <?php echo (isset($campos->id_toma_contacto) and $campos->id_toma_contacto == $toma_contacto->id)?' selected ':'';?>><?php echo $toma_contacto->nombre;?></option>
                <?php endforeach;?>
            </select>
        </div>
     </div>
    <div class="row">

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 ">


          <div id="vue-telefono-cliente">
              <div class="col-lg-12"  style="margin-left:-15px;width: 105%  ">
                  <table class="table table-noline">
                      <thead>
                      <tr>
                          <th width="49%" style="font-weight:bold">Teléfono</th>
                          <th width="41%" ></th>
                          <th width="10%">&nbsp;</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="telefono in asignados_telefonos">
                          <td>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                <input type="hidden" name="telefonos[{{$index}}][id]" value="{{telefono.id}}">
                              <input type="input-left-addon" class="form-control" name="telefonos[{{$index}}][telefono]" id="telefono{{$index}}" v-model="telefono.telefono"  data-rule-required="true" >
                              </div>
                          </td>
                          <td style="padding-left:25px;">
                               <select name="telefonos[{{$index}}][tipo]" id="tipo_telefono{{$index}}" class="form-control select2" v-model="telefono.tipo"   data-rule-required="true" >
                                  <option value="trabajo">Trabajo</option>
                                  <option value="movil">M&oacute;vil</option>
                                  <option value="fax">Fax</option>
                                  <option value="residencial">Residencial</option>
                              </select>
                          </td>
                          <td>
                              <button type="button" class="btn btn-default btn-block" v-show="$index === 0" v-on:click="addFilasTelefono($event)" data-rule-required="true" agrupador="telefono" aria-required="true"><i class="fa fa-plus"></i></button>
                              <button type="button" v-show="$index !== 0" class="btn btn-default btn-block" v-on:click="telefono.length === 1 ?'':deleteFilasTelefono($index)" data-rule-required="true" agrupador="telefono" aria-required="true" ><i class="fa fa-trash"></i></button>
                          </td>
                      </tr>
                      </tbody>
                  </table>
              </div>
          </div>











            <!--<label>Teléfono </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input type="text" name="campo[telefono]" value="<?php echo isset($campos->telefono)?$campos->telefono:'';?>" class="form-control telefono" data-inputmask="'mask': '999-9999', 'greedy':true" id="campo[telefono]">
            </div>-->
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 ">

          <div id="vue-correo-clientes">
              <div class="col-lg-12">
                  <table class="table table-noline">
                      <thead>
                      <tr>
                          <th width="45%" style="font-weight:bold">Correo Electr&oacute;nico</th>
                          <th width="45%"></th>
                          <th width="10%">&nbsp;</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="correo in asignados_correos">
                          <td>
                              <div class="input-group">
                                  <span class="input-group-addon"><b>@</b></span>
                                   <input type="hidden" name="correos[{{$index}}][id]" value="{{correo.id}}">
                              <input type="input-left-addon" class="form-control" name="correos[{{$index}}][correo]" id="correo{{$index}}" v-model="correo.correo"  data-rule-required="true"  data-rule-email="true">
                              </div>
                          </td>
                          <td>
                               <select name="correos[{{$index}}][tipo]" id="tipo_correo{{$index}}" class="form-control select2" v-model="correo.tipo" data-rule-required="true"   >
                                   <option value="trabajo">Trabajo</option>
                                  <option value="personal">Personal</option>
                                  <option value="otro">Otro</option>
                              </select>
                          </td>
                          <td>
                              <button type="button" class="btn btn-default btn-block" v-show="$index === 0" v-on:click="addFilasCorreo($event)" data-rule-required="true" agrupador="correo" aria-required="true"><i class="fa fa-plus"></i></button>
                              <button type="button" v-show="$index !== 0" class="btn btn-default btn-block" v-on:click="correo.length === 1 ?'':deleteFilasCorreo($index)" data-rule-required="true" agrupador="correo" aria-required="true" ><i class="fa fa-trash"></i></button>
                          </td>
                      </tr>
                      </tbody>
                  </table>
              </div>
          </div>



            <!--<label>Correo Electrónico </label>
            <div class="input-group">
                <span class="input-group-addon">@</span>
                <input type="text" name="campo[correo]" value="<?php echo isset($campos->correo)?$campos->correo:'';?>" class="form-control email" id="campo[correo]">
            </div>-->
        </div>

     </div>
    <div class="row">
         <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Comentarios ">
            <label>Observaciones </label>
            <input type="text" name="campo[comentarios]" value="<?php echo isset($campos->comentarios)?$campos->comentarios:'';?>" class="form-control observacion" id="campo[comentarios]">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <a href="<?php echo base_url('clientes_potenciales/listar')?>" class="btn btn-default btn-block" ng-click="limpiarFormulario($event, $flow)" id="cancelar">Cancelar </a>
            <input type="hidden" name="campo[id_cliente_potencial]" value="<?php echo isset($campos->id_cliente_potencial)?$campos->id_cliente_potencial:'';?>">
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="guardar" value="Guardar " class="btn btn-w-m btn-primary btn-block ">
        </div>
    </div>
</div>
