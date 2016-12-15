<?php
//Template::cargar_formulario($info);
$formAttr = array(
    'method' => 'POST',
    'id' => 'crearNotificacionesForm',
    'autocomplete' => 'off'
);
?>
<div id="vistaNotificaciones" class="">
    <div class="tab-content">
        <?php echo form_open(base_url('notificaciones/guardar'), $formAttr); ?>
        <div id="datosnotificacion" class="tab-pane active col-lg-12 col-md-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Crear Notificaci&oacute;n</h5>
                </div>
                <div class="ibox-content" style="display: block;">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>M&oacute;dulo <span required="" aria-required="true">*</span></label>
                            <select data-rule-required="true" name="campo[modulo]" class="form-control" id="modulo" @change="moduloSelect(catalogos.modulos.id)">
                                <option value="">Seleccione</option>
                                <option value="{{catalogos.modulos.id}}">Compras/{{catalogos.modulos.nombre}}</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Transacci&oacute;n <span required="" aria-required="true">*</span></label>
                            <select data-rule-required="true" name="campo[transaccion]" v-model="detalle.transaccion_id" class="form-control" id="transaccion">
                                <option value="">Seleccione</option>
                                "<option v-for="trans in catalogos.transaccion" :value="trans.id_cat" v-html="trans.etiqueta"</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <label>Rol(es) <span required="" aria-required="true">*</span></label>
                            <select name="campo[roles][]" class="chosen" multiple="true" id="roles"  data-placeholder="Seleccione" data-rule-requiredvalidation="true" aria-required="true" v-select2="detalle.rol_id" :config="config.select2">
                                <option value="">Seleccione</option>
                                <option v-for="rol in catalogos.roles" :value="rol.id" v-text="rol.nombre"</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Usuario(s) <span required="" aria-required="true">*</span></label>
                            <select data-rule-required="true" name="campo[usuarios][]" id="usuarios" class="chosen" data-placeholder="Seleccione"  multiple="true" v-select2="detalle.usuario_id" :config="config.select2">
                                <option value="">Seleccione</option>
                                <option v-for="usuario in getUsuarios" :value="usuario.id" >{{usuario.nombre}} {{usuario.apellido}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="ibox-title">
                        <h5>Añade las condiciones (opcional)</h5>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Categor&iacute;as de items</label>
                            <select name="campo[categoria_items]" v-model="detalle.categoria_item_id" class="form-control" id="categoria_items">
                                <option value="">Seleccione</option>
                                <option :value="categoria.id" v-for="categoria in catalogos.categorias">{{categoria.nombre}}</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Operador</label>
                            <select name="campo[operador]" v-model="detalle.operador_id" class="form-control" id="operador">
                                <option value="">Seleccione</option>
                                <option v-for="operador in catalogos.operadores" :value="operador.valor" v-text="operador.etiqueta"</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Monto</label><div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="input-left-addon" name="campo[monto]" value="{{detalle.monto | currency ''}}"  v-model="detalle.monto" class="form-control"  id="monto">
                            </div>
                            <label  for="campo[email]" generated="true" class="error"></label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Sin transacci&oacute;n en (d&iacute;as)</label>
                            <select name="campo[sin_transaccion]" v-model="detalle.trasaccion_dias" class="form-control" id="sin_transaccion">
                                <option value="">Seleccione</option>
                                <!--<option value="{{dia}}" v-for="dia in dias">{{dia}}</option>-->
                                <?php foreach ($info['dias'] as $dias) { ?>
                                    <option value="<?php echo $dias?>" <?php echo ((isset($info['catSelect']))) ? ' selected ' : ''?>><?php echo $dias;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Tipos de Notificaci&oacute;n(es) <span required="" aria-required="true">*</span></label>
                            <select data-rule-required="true" name="campo[tipo_notificacion][]"  class="chosen" id="tipo_notificacion" data-placeholder="Seleccione"  multiple="true" v-select2="detalle.notificacion" :config="config.select2">
                                <option value="">Seleccione</option>
                                <option v-for="tipo in catalogos.notificaciones_tipos" :value="tipo.valor" v-html="tipo.etiqueta"</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Estado <span required="" aria-required="true">*</span></label>
                            <select data-rule-required="true" data-rule-required="true" name="campo[estado]" v-model="detalle.estado_id" class="form-control" id="estado">
                                <option value="">Seleccione</option>
                                <option v-for="estado in catalogos.estados" :value="estado.valor" v-text="estado.etiqueta"</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="tab-pane active col-lg-12 col-md-12">
                        <label>Mensaje personalizado para correo electr&oacute;nico</label>
                        <div class="tab-content m-t-sm">
                            <textarea id="inline-ckeditor" name="campo[mensaje]" class="inline-ckeditor form-control">{{detalle.mensaje}}</textarea>
                        </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('notificaciones/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <input type="button" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="guardar" @click.stop="guardar" :disabled="config.desabilitar">
                            <input type="hidden" name="campo[id]" class="form-control" :value="detalle.id">
                            <input type="hidden" name="campo[empresa_id]" class="form-control" :value="empresa_id">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
