 <div class="tab-content">
        <div id="contacto-9" class="tab-pane active">
            <form action="http://localhost/erp/clientes/ver/11E69097FE1FA6BB84941C872C444624" method="POST"
                  id="crearContacto" autocomplete="off" enctype="multipart/form-data" accept-charset="utf-8"
                  class="ng-pristine ng-valid">
                <input type="hidden" name="erptkn" value="e23457f82bb1053b96003d90cb196646" style="display:none;">
                <?php if (isset($info['cliente'])) { ?>
                <input type="hidden" name="campo[uuid_cliente]" value="<?php echo $info['cliente']['uuid_cliente'] ?>"
                       ng-model="contacto.uuidcliente" id="campo[uuid_cliente]" class="ng-pristine ng-untouched ng-valid">
                <?php } ?>
                <div class="ibox">
                    <div class="ibox-title border-bottom">
                        <h5>Datos del Contacto</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content m-b-sm" style="display: block; border:0px">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre<spanrequired>*</span> ">
                                <label>Nombre <span required="">*</span></label><input type="text" name="campo[nombre]"
                                                                                       value=""
                                                                                       class="form-control ng-pristine ng-untouched ng-valid"
                                                                                       ng-model="contacto.nombre"
                                                                                       data-rule-required="true"
                                                                                       id="campo[nombre]">
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 "><label>Correo <span
                                        required="">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="text" name="campo[correo]" value=""
                                           class="form-control ng-pristine ng-untouched ng-valid"
                                           ng-model="contacto.correo" data-rule-email="true" data-rule-required="true"
                                           id="campo[correo]">

                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 "><label>Celular </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                    <input type="text" name="campo[celular]" value=""
                                           class="form-control ng-pristine ng-untouched ng-valid"
                                           ng-model="contacto.celular"
                                           data-inputmask="'mask': '9999-9999', 'greedy':true" id="campo[celular]">

                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 "><label>Teléfono </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="text" name="campo[telefono]" value=""
                                           class="form-control ng-pristine ng-untouched ng-valid"
                                           ng-model="contacto.telefono"
                                           data-inputmask="'mask': '999-9999', 'greedy':true" id="campo[telefono]">

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Cargo ">
                                <label>Cargo </label><input type="text" name="campo[cargo]" value=""
                                                            class="form-control ng-pristine ng-untouched ng-valid"
                                                            ng-model="contacto.cargo" id="campo[cargo]">
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Direccion ">
                                <label>Direccion </label><input type="text" name="campo[direccion]" value=""
                                                                class="form-control ng-pristine ng-untouched ng-valid"
                                                                ng-model="contacto.direccion" id="campo[direccion]">
                            </div>
                        </div>
                        <!--seccion de identificacion del cliente-->
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Identificacíon "><label>Identificación  </label>
                                <select  name="campo[tipo_identificacion]" class="form-control ng-pristine ng-untouched ng-valid" id="tipo_identificacion1" ng-options="tipo.nombre for tipo in indentificacion track by tipo.tipo"
                                         ng-model="tipos" ng-change="verTipo(tipos)">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-9 col-md-9 col-lg-9 Campos" style="padding-left: 2px">

                                <!--div tipo == pasaporte-->
                                <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3 Pasaporte" ng-class="opcionFormulario.pasaporte === false ? 'hide' : show">
                                    <label>Pasaporte</label>
                                    <input data-rule-required="true" type="text" name="pasaporte[pasaporte]" id="pasaporte[pasaporte]" value=""  class="form-control" ng-disabled="opcionFormulario.pasaporte === false" ng-model="contacto.pasaporte">
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 Cedula" style="padding-left: 1px" ng-class="opcionFormulario.natural === false ? 'hide' : show">
                                    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                        <label>Provincia <span required="" aria-required="true">*</span></label>
                                        <select data-rule-required="true" class="form-control" name="natural[provincia]" id="natural[provincia]" ng-disabled="(naturalLetra.valor === 'PAS' || naturalLetra.valor === 'N' || naturalLetra.valor === 'E' || naturalLetra.valor === 'PE') || opcionFormulario.natural === false" ng-model="contacto.provincia">
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
                                        <select data-rule-required="true" class="form-control" name="natural[letra]" id="natural[letra1]" ng-model="naturalLetra.valor" ng-change="letras(naturalLetra.valor)" ng-disabled="opcionFormulario.natural === false">
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
                                            } ?>" type="text" id="natural[tomo]" name="natural[tomo]" class="form-control" ng-disabled="naturalLetra.valor === 'PAS' || opcionFormulario.natural === false" ng-model="contacto.tomo">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                            <label>Asiento <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php if (isset($info['cliente']['asiento']) && $info['cliente']['tipo_identificacion'] == 'natural') {
                                                echo $info['cliente']['asiento'];
                                            } ?>" type="text" id="natural[asiento]" name="natural[asiento]" class="form-control" ng-disabled="naturalLetra.valor === 'PAS' || opcionFormulario.natural === false" ng-model="contacto.asiento">
                                        </div>
                                    </div>
                                    <div ng-show="naturalLetra.valor === 'PAS'">
                                        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <label>No. Pasaporte <span required="" aria-required="true">*</span></label>
                                            <input data-rule-required="true" value="<?php if (isset($info['cliente']['pasaporte'])) {
                                                echo $info['cliente']['pasaporte'];
                                            } ?>" type="text"  id="natural[pasaporte]" name="natural[pasaporte]" class="form-control" ng-disabled="naturalLetra.valor === null || naturalLetra.valor !== 'PAS'" ng-model="contacto.pasaporte">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Comentarios ">
                                <label>Comentarios </label><input type="text" name="campo[comentarios]" value=""
                                                                  class="form-control ng-pristine ng-untouched ng-valid"
                                                                  ng-model="contacto.comentario"
                                                                  id="campo[comentarios]">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <button name="campo[cancelar]" type="button" class="btn btn-default btn-block"
                                        ng-click="cancelarBtn($event)" id="cancelar">Cancelar
                                </button>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <button name="campo[guardar]" type="button" class="btn btn-primary btn-block"
                                        ng-click="guardarBtn(contacto, tipos, naturalLetra.valor)" id="guardar">Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
