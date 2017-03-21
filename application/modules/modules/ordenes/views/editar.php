<?php if (preg_match("/ordenes/i", self::$ci->router->fetch_class())): ?>
    <div id="wrapper">
        <?php
        Template::cargar_vista('sidebar');
        ?>
        <div id="page-wrapper" class="gray-bg row">

            <?php Template::cargar_vista('navbar'); ?>
            <div class="row border-bottom"></div>
            <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

            <div class="col-lg-12">
                <div id="appOrdenventa" class="wrapper-content">
                    <div class="row">
                        <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php echo!empty($mensaje) ? $mensaje["contenido"] : '' ?>
                        </div>
                    </div>

                    <?php endif; ?>
                    <?php echo modules::run('ordenes/ocultoformulario'); ?>
                    <?php if (preg_match("/ordenes/i", self::$ci->router->fetch_class())): ?>

                    <div class="row" id="subpanel" style="margin-left: -15px;margin-right: -25px;">
                        <?php SubpanelTabs::visualizar($ordencompra_id); ?>
                    </div>

                    <!-- Comentarios -->
                     <div class="row" id="form_crear_ordenes_div">
                         <vista_comments
                          v-if="config.vista === 'editar'"
                          :config="config"
                          :historial.sync="comentario.comentarios"
                          :modelo="comentario.comentable_type"
                          :registro_id="comentario.comentable_id"
                          ></vista_comments>
                        </div>
                     <!-- Comentarios -->

                     <!-- Modal de envio de correo electronico -->
                     <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" v-if="config.vista === 'editar'">
                         <div class="modal-dialog" role="document">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                     <h3 class="modal-title" id="myModalLabel" style="color:#676A6C">
                                         <i class="fa fa-envelope-o" aria-hidden="true" style="font-size:28px;"></i> Enviar: {{detalle.codigo}}
                                     </h3>
                                 </div>
                                 <div class="modal-body">
                                     <div class="form-group">
                                         <label>Proveedor: {{detalle_modal.proveedor}}</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">@</div>
                                             <input class="form-control" v-model="detalle_modal.correo">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                     <button type="button" class="btn btn-primary" @click="enviarFormulario(false)">Guardar</button>
                                     <button type="button" class="btn btn-info" @click="enviarFormulario(true)">Guardar & Enviar</button>
                                 </div>
                             </div>
                         </div>
                     </div>

            <!-- formulario para la vista de historial -->
            <?php
             $formdetalle = array(
              'method'       => 'POST',
              'id'           => 'form_historial',
              'autocomplete' => 'off'
            );
          echo form_open(base_url('ordenes/historial/'.$uuid_orden), $formdetalle);?>
           <input type="hidden" name="id" value="<?php echo $id?>">
          <?php  echo  form_close();?>
                </div>

            </div><!-- cierra .col-lg-12 -->
        </div><!-- cierra #page-wrapper -->
    </div><!-- cierra #wrapper -->

    <?php
    echo Modal::config(array(
        "id" => "optionsModal",
        "size" => "sm"
    ))->html();
    ?>

<?php endif;
