<?php echo form_open_multipart(base_url('documentos/ajax'), "id='formEditNombre'"); ?>
                <input type="hidden" name="documen_id" id="documen_id" class="documen_id">
                <div class="ibox">
                    <div class="ibox-content m-b-sm" style="display: block; border:0px">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Nombre actual</label>
                                <input type="text" name="actual" id="nombre_actual" class="form-control" disabled="" >
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <label>Interes Asegurado</label>
                                <input type="text" name="interes" id="interes_id" class="form-control" disabled="" >
                            </div>
                            <div class="form-group col-xs-12 col-sm-9 col-md-7 col-lg-9">
                                <label>Nombre</label>
                                <input type="text" name="nombre_document" id="nombre_document" class="form-control">
                            </div>
                        <div class="row botones">
                            <div class="col-xs-0 col-sm-3 col-md-3 col-lg-3">&nbsp;</div>
                            
                            <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                <input type="submit" name="campo[guardar]" value="Guardar "
                                class="btn btn-primary btn-block guardarNombre" id="campo[guardar]">
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>