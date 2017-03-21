<div class="ibox-content m-b-sm" style="display: block; border:0px">
    <div class="row">
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Fecha <span aria-required="true" required="">*</span></label>
            <input aria-required="true" name="campo[fecha]" value="<?php echo (isset($campos["fecha"]) and !empty($campos["fecha"])) ? $campos["fecha"] : ''?>" class="form-control" disabled="true" data-rule-required="true" id="campo[fecha]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Origen <span aria-required="true" required="">*</span></label>
            <select aria-required="true" name="campo[origen]" class="chosen" id="origen" data-rule-required="true" disabled="disabled">
                <option value="">Seleccione</option>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Recibido en <span aria-required="true" required="">*</span></label>
            <select aria-required="true" disabled="" name="campo[recibido_en]" class="chosen" id="recibido_en" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach($bodegas as $bodega):?>
                <option value="<?php echo $bodega->uuid_bodega?>" <?php echo (isset($campos["recibido_en"]) and ($campos["recibido_en"] == $bodega->uuid_bodega)) ? 'selected' : ''?>><?php echo $bodega->nombre?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Estado  <span aria-required="true" required="">*</span></label>
            <select aria-required="true" name="campo[estado]" class="chosen estado" id="estado" disabled="disabled" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach($estados as $estado):?>
                <option value="<?php echo $estado->id_cat?>" <?php echo (isset($campos["estado"]) and ($campos["estado"] == $estado->id_cat)) ? 'selected' : ''?>><?php echo $estado->etiqueta?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Número de documento </label>
            <input name="campo[numero_orden]" value="<?php echo (isset($campos["numero_orden"]) and !empty($campos["numero_orden"])) ? $campos["numero_orden"] : ''?>" class="form-control" disabled="true" id="campo[numero_orden]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Comentarios </label>
            <input name="campo[comentarios]" value="<?php echo (isset($campos["comentarios"]) and !empty($campos["comentarios"])) ? $campos["comentarios"] : ''?>" class="form-control comentarios" id="campo[comentarios]" type="text">
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table style="display: block;" id="itemsTable" class="table table-noline tabla-dinamica">
                    <thead>
                        <tr>
                            <th class="item " width="15.833333333333%" colspan="2">
                                Item 
                            </th>
                            <th class="descripcion " width="15.833333333333%">
                                Descripción 
                            </th>
                            <th class="observacion " width="15.833333333333%">
                                Observación 
                            </th>
                            <th class="cantidad_esperada " width="15.833333333333%">
                                Cantidad esperada 
                            </th>
                            <th class="cantidad_recibida " width="15.833333333333%">
                                Cantidad recibida 
                            </th>
                            <th class="unidad " width="15.833333333333%">
                                Unidad 
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($campos["items"] as $i => $fila):?>
                        <?php $serializable = $fila["tipo_id"] == "5" || $fila["tipo_id"] == "8";?>
                        <tr id="items<?php echo $i;?>">
                            <?php if($serializable):?>
                            <td style="width: 1%;">
                                <i class="fa fa-caret-right" style="font-size: 28px;"></i>
                                <i class="fa fa-caret-down hide" style="font-size: 28px;"></i>
                            </td>
                            <?php endif;?>
                            <td class="item0" <?php echo (!$serializable)?' colspan="2" ':''?>>
                                <select style="display: none;" disabled="" name="items[<?php echo $i;?>][item]" class="chosen item" id="item<?php echo $i;?>">
                                    <option value="">Seleccione</option>
                                    <?php foreach($items as $item):?>
                                    <option value="<?php echo $item->uuid_item?>" <?php echo ($fila["item"] == $item->uuid_item) ? 'selected' : ''?>><?php echo $item->nombre_completo?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                            <td class="descripcion<?php echo $i;?> ">
                                <input aria-required="true" name="items[<?php echo $i;?>][descripcion]" value="<?php echo $fila["descripcion"];?>" class="form-control" disabled="true" data-rule-required="true" id="descripcion<?php echo $i;?>" type="text">
                            </td>
                            <td class="observacion<?php echo $i;?> ">
                                <input aria-required="true" name="items[<?php echo $i;?>][observacion]" value="<?php echo $fila["observacion"];?>" class="form-control" disabled="true" data-rule-required="true" id="observacion<?php echo $i;?>" type="text">
                            </td>
                            <td class="cantidad_esperada<?php echo $i;?> ">
                                <input aria-required="true" name="items[<?php echo $i;?>][cantidad_esperada]" value="<?php echo $fila["cantidad_esperada"];?>" class="form-control" disabled="true" data-rule-required="true" id="cantidad_esperada<?php echo $i;?>" type="text">
                            </td>
                            <td class="cantidad_recibida<?php echo $i;?> ">
                                <input aria-required="true" name="items[<?php echo $i;?>][cantidad_recibida]" value="<?php echo $fila["cantidad_recibida"];?>" <?php echo ($serializable) ? 'disabled=""' : '' ;?> class="form-control cantidad_recibida" data-inputmask="'mask':'9{1,4}[.*9{1,4}]','greedy':false" data-rule-required="true" id="cantidad_recibida<?php echo $i;?>" type="text">
                            </td>
                            <td class="unidad<?php echo $i;?> ">
                                <select style="display: none;" disabled="" name="items[<?php echo $i;?>][unidad]" class="chosen unidad" id="unidad<?php echo $i;?>">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($fila["unidades"] as $unidad):?>
                                    <option value="<?php echo $unidad->uuid_unidad?>" <?php echo ($unidad->uuid_unidad == $fila["unidad"]) ? 'selected' : ''?>><?php echo $unidad->nombre?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                            <td class="hide">
                                <input name="items[<?php echo $i;?>][id_entrada_item]" value="<?php echo $fila["id_entrada_item"];?>" type="hidden">
                            </td>
                        </tr>
                        <?php if($serializable):?>
                        <tr id="items<?php echo $i;?>Seriales" class="hide">
                            <td colspan="7">
                                <table style="width: 100%;background-color: #A2C0DA">
                                    <?php 
                                        $seriales   = $fila["cantidad_esperada"];
                                        $filas      = $seriales/5;
                                        $contador2  = 0;
                                    ?>
                                    <?php for($filas; $filas > 0; $filas--):?>
                                    <tr>
                                        <?php $contador = 0;?>
                                        <?php for($seriales; $seriales > 0; $seriales--):?>
                                        <td style="padding: 15px !important">
                                            <input type="text" class="form-control" name="items[<?php echo $i;?>][seriales][]" value="<?php echo isset($fila["seriales"][$contador2]) ? $fila["seriales"][$contador2]["nombre"] : '';?>">
                                        </td>
                                        <?php
                                            $contador++;
                                            $contador2++;
                                            if($contador == 5){
                                                $seriales--;
                                                break;
                                            }
                                        ?>
                                        <?php endfor;?>
                                    </tr>
                                    <?php endfor;?>
                                </table>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <span class="tabla_dinamica_error"></span>
            </div>
        </div>
    </div>
    
    <div class="row"></div>
    
    <div class="row"> 
        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <a href="<?php echo base_url("entradas/listar");?>" class="btn btn-default btn-block" id="cancelarEntrada">Cancelar </a> 
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input name="campo[guardarEntrada]" value="Guardar " class="btn btn-primary btn-block btnGuardar" id="campo[guardarEntrada]" type="submit">
            <input type="hidden" name="campo[entrada_id]" value="<?php echo $campos["entrada_id"]?>">
        </div>
    </div>
    
</div>

<style type="text/css">
    table#itemsTable thead th {
        background-color: #0076BE;
        color: white;
        border: 1px solid white !important;
        font-weight: bold;
        padding-left: 7px !important;
    }
</style>
    