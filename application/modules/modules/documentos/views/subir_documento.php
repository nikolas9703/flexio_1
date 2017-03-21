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
                <div class="row">
                    <div
                        class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : '' ?>
                    </div>
                </div>
                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <?php
                            $formAttr = array(
                                'method'       => 'POST',
                                'id'           => 'subirDocumentoFacturaForm',
                                'autocomplete' => 'off',
                                'enctype'      => 'multipart/form-data'
                            );
                            echo form_open(base_url("documentos/guardar"), $formAttr);
                            ?>
                            <div class="ibox-content m-b-sm" style="display: block; border:0px">
                                <div class="row">
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="">Proveedor</label>
                                        <select name="campo[proveedor]" class="form-control select2" id="proveedor"
                                                data-rule-required="true" disabled>
                                                <option value="<?php echo $proveedor["id"] ?>"><?php echo $proveedor["nombre"]; ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="">N&uacute;mero de factura de compra</label>
                                        <select name="campo[numero_factura]" class="form-control select2" id="numero_factura"
                                                data-rule-required="true" disabled>
                                            <option value="<?php echo $no_factura ?>"><?php echo $no_factura; ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="">Centro Contable</label>
                                        <select name="campo[centro_contable_id]" class="form-control select2" id="centro_contable_id"
                                                data-rule-required="true" disabled>
                                            <option value="<?php echo $centro_contable["id"] ?>"><?php echo $centro_contable["nombre"]; ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                        <label for="fecha_hasta">Fecha documento </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="campo[fecha_documento]" class="form-control"
                                                   id="fecha_documento" data-rule-required="true" value="<?php echo $fecha ?>">
                                        </div>
                                        <label id="fecha_hasta-error" class="error" for="fecha_hasta"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="">Tipo de documento <span required="" aria-required="true">*</span></label>
                                        <select name="campo[tipo_id]" class="form-control select2"
                                                id="tipo_id" data-rule-required="true">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($tipo_documento as $tipo) { ?>
                                                <option value="<?php echo $tipo["id"] ?>"><?php echo $tipo["nombre"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="">Usuario</label>
                                        <select name="campo[usuario]" class="form-control select2" id="usuario"
                                                data-rule-required="true" disabled>
                                            <option value="<?php echo $usuario["id"] ?>"><?php echo $usuario["nombre"]; ?></option>
                                        </select>
                                    </div>
                                  <!--  <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                        <label for="">Estado</label>
                                        <select name="campo[etapa]" class="form-control select2" id="etapa"
                                                data-rule-required="true">
                                            <option value="">Seleccione</option>
                                            <option value="por_enviar">Por enviar</option>
                                            <option value="no_se_envia">No se envía</option>
                                            <option value="enviado">Enviado</option>
                                        </select>
                                    </div> -->
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-lg-12">
                                        <label>&nbsp;</label>
                                        <div id="dropTarget" class="drop p-lg text-center"
                                             style="border: 2px dotted #ccc; text-">
                                            <!--<button id="documento" class="btn btn-outline btn-default align-center {{fileClassBtn}}" type="button" ng-bind-html="fileBtn">Seleccionar</button>-->
                                            <span class="btn btn-outline btn-default align-center {{fileClassBtn}} fileinput-button">
		                                    <span ng-bind-html="fileBtn">Seleccionar</span>
                                                <!-- The file input field used as target for the file upload widget -->
		                                    <input id="documento" type="file" name="documentos[]" class="fileinput-button" multiple data-rule-required="true">
		                                   </span>
                                            <b>o Arrastre el archivo aqui</b>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <input type="button" id="cancelarBtn" class="btn btn-default btn-block" onclick="history.back()"
                                               value="Cancelar"/>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <input type="hidden" name="campo[factura_id]" value="<?php echo isset($factura_id)?$factura_id:'';?>">
                                        <input type="submit" id="guardarBtn" class="btn btn-primary btn-block"
                                               value="Guardar"/>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div><!-- cierra .col-lg-12 -->
</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
?>

