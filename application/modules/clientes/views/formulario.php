<?php
$info = !empty($info) ? $info : array();
//Template::cargar_formulario($info);
$formAttr = array(
    'method' => 'POST',
    'id' => 'formClienteCrear',
    'autocomplete' => 'off'
);
?>
<div ng-controller="clienteFormularioController">
    <div id="vistaCliente" class="">

        <div class="tab-content">
            <?php echo form_open(base_url('clientes/guardar'), $formAttr); ?>
            <div id="datosdelcliente-5" class="tab-pane active col-lg-12 col-md-12">

                <?php if (isset($info['cliente'])) { ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" value="<?php echo $info['cliente']['uuid_cliente'] ?>">
                <?php } ?>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Datos del Cliente</h5>
                    </div>
                    <div class="ibox-content" style="display: block;">
                        <div class="row"ng-class="balance === true ? 'hide' : 'show'">
                            <!--<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Codigo hidden">
                                <label>No. Cliente </label>-->
                                <input type="hidden" name="campo[codigo]" value="<?php echo $info['codigo'] ?>" class="form-control" id="campo[codigo]" disabled>
                           <!-- </div>-->
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 NombredelCliente "><label>Nombre del Cliente <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" value="<?php if (isset($info['cliente'])) {
                                    echo $info['cliente']['nombre'];
                                } ?>" class="form-control" id="campo[nombre]" data-rule-required="true">
                            </div>
                        </div>
                        <div class="row" ng-class="balance === true ? 'show' : 'hide'" ng-if="balance">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 NombredelCliente "><label>Nombre del Cliente <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" value="<?php if (isset($info['cliente'])) {
                                    echo $info['cliente']['nombre'];
                                } ?>" class="form-control" id="campo[nombre]" data-rule-required="true">
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 "><label></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" disabled name="campo[saldo]" value="<?php if (isset($info['cliente'])) {
                                        echo number_format(str_replace(',', '', $info['cliente']['saldo_pendiente']), 2);
                                    } ?>" class="form-control debito moneda"  id="campo[saldo]">
                                </div>
                                <label class="label-danger-text">Saldo por cobrar</label>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                                <label></label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon" disabled name="campo[lcredito]" value="<?php if (isset($info['cliente'])) {
                                        echo $info['cliente']['credito_favor'];
                                    } ?>" class="form-control debito moneda" id="campo[lcredito]">
                                </div>
                                <label class="label-success-text">Crédito a favor</label>
                            </div>
                        </div>

                        <!--seccion de identificacion del cliente-->
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Identificacíon "><label>Identificación  </label>
                                <select  name="campo[tipo_identificacion]" class="form-control" id="tipo_identificacion" ng-options="tipos.nombre for tipos in indentificacion track by tipos.tipo"
                                         ng-model="tipos" ng-change="verTipo(tipos)">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-9 col-md-9 col-lg-9">

                                <!--div tipo == pasaporte-->
                                <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3" ng-class="opcionFormulario.pasaporte === false ? 'hide' : show">
                                    <label>Pasaporte</label>
                                    <input data-rule-required="true" type="text" name="pasaporte[pasaporte]" id="pasaporte[pasaporte]" value="<?php echo (isset($info['cliente']['identificacion']) && $info['cliente']['tipo_identificacion'] == 'pasaporte') ? $info['cliente']['identificacion'] : ''; ?>"  class="form-control" ng-disabled="opcionFormulario.pasaporte === false">
                                </div>

                                <!-- div oculto para enseñar campos juridico natural -->
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-class="opcionFormulario.juridico === false ? 'hide' : show">
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                        <label>Tomo/Rollo <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" name="juridico[tomo]" id="juridico[tomo]" value="<?php if (isset($info['cliente']['tomo']) && $info['cliente']['tipo_identificacion'] == 'juridico') {
                                            echo $info['cliente']['tomo'];
                                        } ?>"  class="form-control" ng-disabled="opcionFormulario.juridico === false">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                        <label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" name="juridico[folio]" value="<?php if (isset($info['cliente']['folio']) && $info['cliente']['tipo_identificacion'] == 'juridico') {
                                            echo $info['cliente']['folio'];
                                        } ?>" id="juridico[folio]" class="form-control" ng-disabled="opcionFormulario.juridico === false">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                        <label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" value="<?php if (isset($info['cliente']['asiento']) && $info['cliente']['tipo_identificacion'] == 'juridico') {
                                            echo $info['cliente']['asiento'];
                                        } ?>" name="juridico[asiento]" id="juridico[asiento]" class="form-control" ng-disabled="opcionFormulario.juridico === false">
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                        <label>Digito verificador <span required="" aria-required="true">*</span></label>
                                        <input data-rule-required="true" type="text" value="<?php if (isset($info['cliente']['verificador']) && $info['cliente']['tipo_identificacion'] == 'juridico') {
                                            echo $info['cliente']['verificador'];
                                        } ?>" name="juridico[verificador]" id="juridico[verificador]" class="form-control" ng-disabled="opcionFormulario.juridico === false">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-class="opcionFormulario.natural === false ? 'hide' : show">
                                    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <label>Provincia <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control" name="natural[provincia]" id="natural[provincia]" ng-disabled="(naturalLetra.valor === 'PAS' || naturalLetra.valor === 'N' || naturalLetra.valor === 'E' || naturalLetra.valor === 'PE') || opcionFormulario.natural === false">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($info['provincias'] as $provincia) { ?>
                                                <option <?php if (isset($info['cliente']['provincia'])) {
                                                    if ($provincia->id == $info['cliente']['provincia']) {
                                                        echo ' selected';
                                                    }
                                                } ?> value="<?php echo $provincia->id ?>"><?php echo $provincia->valor ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <label>Letras <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control" name="natural[letra]" id="natural[letra]" ng-model="naturalLetra.valor" ng-change="letras(naturalLetra.valor)" ng-disabled="opcionFormulario.natural === false">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($info['letras'] as $letra) { ?>
                                                <option value="<?php echo $letra->key ?>"><?php echo $letra->valor ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div ng-show="naturalLetra.valor === null || naturalLetra.valor !== 'PAS'">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label>Tomo <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php if (isset($info['cliente']['tomo']) && $info['cliente']['tipo_identificacion'] == 'natural') {
                                                echo $info['cliente']['tomo'];
                                            } ?>" type="text" id="natural[tomo]" name="natural[tomo]" class="form-control" ng-disabled="naturalLetra.valor === 'PAS' || opcionFormulario.natural === false">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label>Asiento <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php if (isset($info['cliente']['asiento']) && $info['cliente']['tipo_identificacion'] == 'natural') {
                                                echo $info['cliente']['asiento'];
                                            } ?>" type="text" id="natural[asiento]" name="natural[asiento]" class="form-control" ng-disabled="naturalLetra.valor === 'PAS' || opcionFormulario.natural === false">
                                        </div>
                                    </div>
                                    <div ng-show="naturalLetra.valor === 'PAS'">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <label>No. Pasaporte <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php if (isset($info['cliente']['pasaporte'])) {
                                                echo $info['cliente']['pasaporte'];
                                            } ?>" type="text"  id="natural[pasaporte]" name="natural[pasaporte]" class="form-control" ng-disabled="naturalLetra.valor === null || naturalLetra.valor !== 'PAS'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12" style="margin-left:-15px;">
                                <div class="ibox-content no-padding">
                                    <div id="vue-telefono-cliente">
                                        <div class="col-lg-6">
                                            <table class="table table-noline">
                                                <thead>
                                                <tr>
                                                    <th width="45%" style="font-weight:bold">Tel&eacute;fono</th>
                                                    <th width="45%" ></th>
                                                    <th width="10%">&nbsp;</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <tr v-for="telef in asignados_telefonos">
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                        <input type="hidden" name="telefonos[{{$index}}][telefono]" value="{{telef.telefono}}">
                                                        <input type="input-left-addon" class="form-control telefono" name="telefonos[{{$index}}][telefono]" id="telefonos{{$index}}" v-model="telef.telefono">
                                                        </div>
                                                    </td>
                                                    <td>

                                                        <select name="telefonos[{{$index}}][tipo]" id="tipo{{$index}}" class="form-control" v-model="telef.tipo">
                                                            <option value="">Seleccione</option>
                                                            <option value="trabajo">Trabajo</option>
                                                            <option value="movil">M&oacute;vil</option>
                                                              <option value="fax">Fax</option>
                                                            <option value="residencial">Residencial</option>
                                                            <option value="otro">Otro</option>

                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-default btn-block" v-show="$index === 0" v-on:click="addFilasTelefono($event)" data-rule-required="true" agrupador="telef" aria-required="true"><i class="fa fa-plus"></i></button>
                                                        <button type="button" v-show="$index !== 0" class="btn btn-default btn-block" v-on:click="telef.length === 1 ?'':deleteFilasTelefono($index)" data-rule-required="true" agrupador="telef" aria-required="true" ><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Teléfono </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="input-left-addon" name="campo[telefono]" value="<?php if (isset($info['cliente'])) {
                                    echo $info['cliente']['telefono'];
                                } ?>" class="form-control telefono_clientes" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]">
                            </div>
                        </div>-->
                                <div id="vue-correo-clientes">
                                    <div class="col-lg-6">
                                        <table class="table table-noline">
                                            <thead>
                                            <tr>
                                                <th width="45%" style="font-weight:bold">Correo Electr&oacute;nico <span required="" aria-required="true">*</span></th>
                                                <th width="45%"></th>
                                                <th width="10%">&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="correo in asignados_correos">
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">@</span>
                                                    <input type="hidden" name="correos[{{$index}}][correo]" value="{{correo.correo}}">
                                                    <input type="input-left-addon" class="form-control" name="correo[{{$index}}][correo]" id="correo{{$index}}" v-model="correo.correo"  data-rule-required="true" data-rule-email="true">
                                                    </div>
                                                </td>
                                                <td>

                                                    <select name="correos[{{$index}}][tipo]" id="tipo{{$index}}" class="form-control" v-model="correo.tipo">
                                                        <option value="">Seleccione</option>
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
                                <!-- <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Correo Electrónico <span required="" aria-required="true">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="input-left-addon" name="campo[correo]" value="<?php if (isset($info['cliente'])) {
                                    echo $info['cliente']['correo'];
                                } ?>" data-rule-required="true" data-rule-email="true" class="form-control debito"  id="campo[correo]">
                                </div>
                                <label  for="campo[correo]" generated="true" class="error"></label>
                            </div>-->
                            </div>
                        </div>
                        <!--/seccion de identificacion del cliente-->


                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 SitioWeb hidden">
                                <label>Sitio Web </label>
                                <input type="text" name="campo[web]" value="<?php if (isset($info['cliente'])) {
                                    echo $info['cliente']['web'];
                                } ?>" class="form-control" id="campo[web]">
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 "><label>Límite de crédito </label><div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="input-left-addon moneda" name="campo[credito_limite]" value="<?php if (isset($info['cliente'])) {
                                        echo $info['cliente']['credito_limite'];
                                    } ?>" class="form-control limite_credito" id="campo[credito_limite]">
                                </div>
                                <label  for="campo[credito_limite]" generated="true" class="error"></label>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Exonerado de Impuesto </label><div class="input-group">

                                    <span class="input-group-addon"><input ng-model="impuesto" type="checkbox" name="impuesto_checkbox" id="impuesto_checkbox"  /></span>
                                    <input type="text"  name="campo[exonerado_impuesto]" value="<?php if (isset($info['cliente'])) {
                                        echo $info['cliente']['exonerado_impuesto'];
                                        } ?>" class="form-control" onclick="this.placeholder=''"  ng-disabled="!impuesto" data-rule-required="true" placeholder="Ingrese número de certificado"/>
                                </div>
                                <label  for="campo[exonerado_impuesto]" generated="true" class="error"></label>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 TomaContacto ">
                                <label>Toma Contacto <span required="" aria-required="true">*</span></label>
                                <select name="campo[toma_contacto_id]" class="form-control" id="contacto_tipo" data-rule-required="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['toma_contacto'] as $contacto) { ?>
                                        <option <?php if (isset($info['cliente'])) {
                                            if ($contacto->id == $info['cliente']['toma_contacto_id']) {
                                                echo ' selected';
                                            }
                                        } ?> value="<?php echo $contacto->id ?>"><?php echo $contacto->nombre ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 TipoCliente ">
                                <label>Tipo de cliente </label>
                                <select name="campo[tipo]" class="form-control" id="tipo">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['tipo'] as $tipo) { ?>
                                        <option <?php if (isset($info['cliente'])) {
                                            if ($tipo->id == $info['cliente']['tipo']) {
                                                echo ' selected';
                                            }
                                        } ?> value="<?php echo $tipo->id ?>"><?php echo $tipo->nombre ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 CategoriaCliente ">
                                <label>Categor&iacute;a de cliente </label>
                                <select name="campo[categoria]" class="form-control" id="categoria">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['categoria'] as $categoria) { ?>
                                        <option <?php if (isset($info['cliente'])) {
                                            if ($categoria->id == $info['cliente']['categoria']) {
                                                echo ' selected';
                                            }
                                        } ?> value="<?php echo $categoria->id ?>"><?php echo $categoria->nombre ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 EstadoCliente ">
                                <label>Estado </label>
                                <select name="campo[estado]" class="form-control" id="estado">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['estado'] as $estado) { ?>
                                        <option <?php if (isset($info['cliente'])) {
                                            if ($estado->etiqueta == $info['cliente']['estado']) {
                                                echo ' selected';
                                            }
                                        }elseif ($estado->etiqueta == 'por_aprobar'){  echo ' selected';} ?> value="<?php echo $estado->etiqueta ?>"><?php echo $estado->valor ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Comentarios ">
                                <label>Observaciones </label>
                                <input type="text" name="campo[comentario]" value="<?php if (isset($info['cliente'])) { echo $info['cliente']['comentario'];}else{echo '';} ?>" class="form-control" id="campo[comentario]">
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Dirección hidden" >
                                <label>Dirección</label>
                                <input type="text" name="campo[direccion]" value="<?php if (isset($info['cliente'])) { echo $info['cliente']['direccion']; } ?>" class="form-control" id="campo[direccion]">
                            </div>
                        </div>
                    </div></div>

            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Centros facturables</h5>

                        </div>
                        <div class="ibox-content no-padding">
                            <div id="vue-centros-facturables" class="row">
                                <div class="col-lg-12">
                                    <table class="table table-noline">
                                        <thead>
                                        <tr>
                                            <th width="45%">Centro de facturaci&oacute;n<span required="" aria-required="true">*</span></th>
                                            <th width="45%">Direcci&oacute;n<span required="" aria-required="true">*</span></th>
                                            <th width="10%">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="items in cliente_centros">
                                            <td>
                                                <input type="hidden" name="centros[{{$index}}][id]" value="{{items.id}}">
                                                <input data-rule-required="true" type="text" class="form-control" name="centros[{{$index}}][nombre]" id="nombre_centro{{$index}}" v-model="items.nombre">
                                            </td>
                                            <td><input data-rule-required="true" type="text" class="form-control" name="centros[{{$index}}][direccion]" id="direccion_centro{{$index}}" v-model="items.direccion"></td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-block" v-show="$index === 0" v-on:click="addFilas($event)" data-rule-required="true" agrupador="items" aria-required="true"><i class="fa fa-plus"></i></button>
                                                <button  type="button" v-show="$index !== 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="items.length === 1 ?'':deleteFilas($index,items.id)"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Asignados al cliente</h5>

                        </div>
                        <div class="ibox-content no-padding">
                            <div id="vue-asignados-cliente" class="row">
                                <div class="col-lg-12">
                                    <table class="table table-noline">
                                        <thead>
                                        <tr>
                                            <th width="45%">L&iacute;nea de negocio</th>
                                            <th width="45%">Asignado a</th>
                                            <th width="10%">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="asig in asignados_clientes">
                                            <td>
                                                <input type="hidden" name="asignados[{{$index}}][id]" value="{{asig.id}}">
                                                <input type="text" class="form-control" name="asignados[{{$index}}][linea_negocio]" id="linea_negocio{{$index}}" v-model="asig.linea_negocio"  >
                                            </td>
                                            <td>

                                                <select name="asignados[{{$index}}][usuario_id]" id="usuario_id{{$index}}" class="form-control" v-model="asig.usuario_id"    >
                                                    <option value="">Seleccione</option>
                                                    <?php foreach ($info['asignados'] as $asignado) { ?>
                                                        <option value="<?php echo $asignado->id; ?>"><?php echo $asignado->nombre.' '.$asignado->apellido; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-block" v-show="$index === 0" v-on:click="addFilasAsignados($event)" data-rule-required="true" agrupador="asig" aria-required="true"><i class="fa fa-plus"></i></button>
                                                <button type="button" v-show="$index !== 0" class="btn btn-default btn-block" v-on:click="asig.length === 1 ?'':deleteFilasAsignados($index,asig.id)" data-rule-required="true" agrupador="asig" aria-required="true" ><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6"></div>
            </div>
            <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('clientes/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                    <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block guardarCliente" id="campo[guardar]">
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
