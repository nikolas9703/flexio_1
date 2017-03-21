
<div class="row">


        <div id="login-box" class="form-box">
            <div class="alert-danger alert-dismissable <?php echo !empty($mensaje_error) ? 'show' : 'hide'  ?>" style="padding:10px;">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <p class="lead" style="padding:0px;margin:0px;"><i class="fa fa-fw fa-save"></i><?php echo $mensaje_error; ?></p>
            </div>
            <div class="alert-success alert-dismissable <?php echo !empty($mensaje_activacion) ? 'show' : 'hide'  ?>" style="padding:10px;">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <p class="lead" style="padding:0px;margin:0px;"><i class="fa fa-fw fa-save"></i><?php echo $mensaje_activacion; ?></p>
            </div>
        <?php  $this->session->unset_userdata('mensaje_error_activacion');
                $this->session->unset_userdata('mensaje_activacion');
        ?>
             <div class="header">Valida Codigo Enlace</div>
            	<?= form_open("generadorcodigo/valida_codigo_existe_documento", array("class" => "form-signin", "autocomplete" => "off") )?>
                <div class="body bg-dark-green">







                    <div class="form-group">
                        <div class="controls">
                            <label>Codigo del Documento:</label>
                            <div class="input-group col-sm-12">
                                    <input type="input" name="codigo_documento" class="form-control"  required>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="footer bg-dark-green">
                    <button class="btn btn-default btn-block" type="submit">Validar</button>
                    <button class="btn btn-default btn-block" type="cancel">Cancelar</button>



                </div>
            <?= form_close() ?>

            <div class="header">Valida Codigo Datos</div>
            	<?= form_open("generadorcodigo/valida_codigo_no_cambia_documento", array("class" => "form-signin", "autocomplete" => "off") )?>
                <div class="body bg-dark-green">

                    <div class="form-group">
                        <div class="controls">
                            <label> Tipo de Documento: </label>
                            <div class="input-group col-sm-12">
                                <select class="form-control" name="tipo_documento" id="tipo_documento" >
                                    <option value="OC" selected>Orden de Compra</option>
                                    <option value="OV">Orden de Venta</option>
                                </select>
                            </div>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="controls">
                            <label id="etiqueta_numero_documento"> No. Documento: </label>
                            <div class="input-group col-sm-12">
                                    <input type="input" name="numero_documento" class="form-control"  required>

                            </div>
                        </div>
                    </div>



                  <!--  <div class="form-group">
                        <div class="controls">
                            <label>Cantidad de Items:</label>
                            <div class="input-group col-sm-12">
                                    <input type="input" name="cantidad_items" class="form-control"  required>

                            </div>
                        </div>
                    </div>-->

                    <div class="form-group">
                        <div class="controls">
                            <label> Monto:</label>
                            <div class="input-group col-sm-12">
                                    <input type="input" name="monto_documento" class="form-control"  required>

                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="controls">
                            <label> Fecha:</label>
                            <div class="input-group col-sm-12">
                                    <input type="input" name="fecha_documento" class="form-control"  required>

                            </div>
                        </div>
                    </div>



                </div>
                <div class="footer bg-dark-green">
                    <button class="btn btn-default btn-block" type="submit">Validar</button>
                    <button class="btn btn-default btn-block" type="cancel">Cancelar</button>



                </div>
            <?= form_close() ?>
           <!-- </form> -->

</div>
