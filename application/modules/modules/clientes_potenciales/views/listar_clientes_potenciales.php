<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content">
              <div class="row" ng-controller="toastController">
               <?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
                 <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                     <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                 </div>
             </div>

                <div class="row">
                    <!-- BUSCADOR -->


                    <div class="ibox border-bottom">
                        <div class="ibox-title">
                            <h5>Buscar Cliente Potencial</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>
                        <div class="ibox-content" style="display:none;">
                            <!-- Inicia campos de Busqueda -->
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label for="">Nombre del Cliente Potencial</label>
                                    <input type="text" id="nombre" class="form-control" value="" placeholder="">
                                </div>
                                <!-- <div class="form-group col-sm-3">
                                     <label for="">Compa&ntilde;ia</label>
                                     <input type="text" id="compania" class="form-control" value="" placeholder="">
                                 </div>-->
                                <div class="form-group col-sm-3">
                                    <label for="">Tel&eacute;fono</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input id="telefono" value="" class="form-control" data-inputmask="'mask': '999-9999', 'greedy':true" type="input-left-addon">
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="">E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">@</span>
                                        <input aria-required="true" id="correo" value="" data-rule-email="true" class="form-control" type="input-left-addon">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-0 col-sm-6 col-md-6">&nbsp;</div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-6" style="text-align:right;" >
                                    <input type="button" id="searchBtn" class="btn btn-w-m btn-default" value="Filtrar" />
                                    <input type="button" id="clearBtn" class="btn btn-w-m btn-default" value="Limpiar" />
                                </div>
                                <!--  <div class="form-group col-xs-12 col-sm-3 col-md-3">
                                        <input type="button" id="clearBtn" class="btn btn-w-m btn-default btn-block" value="Limpiar" />
                                </div>-->
                            </div>
                            <!-- Termina campos de Busqueda -->
                        </div>
                    </div>
                </div>
                <!-- /BUSCADOR -->

                <!-- JQGRID -->
                <?php echo modules::run('clientes_potenciales/ocultotabla'); ?>

                <!-- /JQGRID -->
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php
$formAttr = array('method' => 'POST', 'id' => 'exportarClientesPotenciales', 'autocomplete' => 'off');
echo form_open(base_url('clientes_potenciales/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>
<?php
echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
))->html();
?> <!-- modal opciones -->
