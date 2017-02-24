<?php

                            $formAttr = array ('method' => 'POST',
                            'id' => 'formularioPoliticas',
                            'autocomplete' => 'off',);
		echo form_open (base_url(uri_string()), $formAttr);
                            ?>
                             <input type="hidden" name="campo[id]" id="id" value="" >

                             <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear pol&iacute;tica de transacciones</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Formulario Principal -->
                                       <div class="row">

                                            <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                   <input type="hidden" name="campo[empresa_id]" id="empresa_id" value="{{detalle.empresa_id}}" >
                                                   <label for="">Nombre <span required="" aria-required="true">*</span></label>
                                                   <input type="text" name="campo[nombre]" id="nombre" class="form-control" data-rule-required="true" v-model="formulario.nombre">
                                               </div>

                                               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                   <label for="">Rol <span required="" aria-required="true">*</span></label>
                                                   <select v-select="formulario.role_id" v-model="formulario.role_id" name="campo[role_id]" class="form-control select2" id="role_id" data-rule-required="true">
                                                       <option value="">Seleccione</option>
                                                       <option v-for="rol in catalogos.roles" v-bind:value="rol.id">
                                                        {{rol.nombre}}
                                                      </option>
                                                   </select>
                                               </div>

                                               <!--  -->

                                               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                   <label for="">M&oacute;dulo <span required="" aria-required="true">*</span></label>
                                                   <select v-select="formulario.modulo" v-model="formulario.modulo" name="campo[modulo]" class="form-control select2" id="modulo_id" data-rule-required="true">
                                                      <option value="">Seleccione</option>
                                                       <option v-for="modulo in catalogos.modulos" v-bind:value="modulo.id">
                                                        {{modulo.nombre}}
                                                      </option>
                                                   </select>
                                               </div>

                                               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                    <label for="">Transacci&oacute;n <span required="" aria-required="true">*</span></label>
                                                   <select v-select="formulario.politica_estado" v-model="formulario.politica_estado" name="campo[politica_estado]" class="form-control select2" id="transaccion_id" data-rule-required="true" :disabled="disabledEstado">
                                                       <option value="">Seleccione</option>
                                                       <option v-for="transaccion in catalogoDinamicoEstados" v-bind:value="transaccion.id">
                                                        {{transaccion.etiqueta}}
                                                      </option>
                                                   </select>
                                               </div>

                                           </div>
                                           <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" v-show="showCategorias">
                  <label for="">Categor&iacute;a(s) <span required="" aria-required="true">*</span></label>
                   <select multiple name="categorias[]" class="form-control select2" id="categoria_id" data-rule-required="true" v-select="formulario.categorias" v-model="formulario.categorias"  data-placeholder="Seleccione">
                      <option  v-if="catalogos.categorias.length > 0" value="todas">Todas las categorias</option>
                      <option v-for="categoria in catalogos.categorias" v-bind:value="categoria.id">
                       {{categoria.nombre}}
                     </option>
                     </select>
                </div>

                                               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" v-if="formulario.modulo !=='pedido' && formulario.modulo !=='aseguradora' && formulario.modulo !=='ramos' && formulario.modulo !=='ajustadores' && formulario.modulo !=='agentes' && formulario.modulo !=='intereses_asegurados' && formulario.modulo !=='solicitudes' && formulario.modulo !=='polizas'">
                                                   <label for="">Monto l&iacute;mite (hasta) <span required="" aria-required="true">*</span></label>
                                                    <div class="input-group">
                                                       <span class="input-group-addon">$</span>
                                                       <input type="input-left-addon"  name="campo[monto_limite]" value="" class="form-control moneda"  id="monto_limite"  data-rule-required="true">
                                                   </div>
                                               </div>



                                               <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                                   <label for="">Estado</label>
                                                   <select name="campo[estado_id]" class="form-control select2" id="estado_id">
                                                       <option value="1">Activo</option>
                                                       <option value="0">Inactivo</option>

                                                   </select>
                                               </div>

                                           </div>

                                       </div>

                                    <div class="row">


                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="clearBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                             <input type="button" id="guardarBtn"  @click="guardar" class="btn btn-primary btn-block" value="Guardar">
                                             <!-- <input type="button" id="guardarBtn" class="btn btn-primary btn-block" value="Guardar" v-on:click="greet"/> -->
                                         </div>
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                       <?php echo form_close(); ?>
