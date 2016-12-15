<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" style="padding-top: 7px;">
        <span><strong>Empezar traslado desde </strong></span>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <select class="form-control time<?php echo time();?>" name="empezar_tipo" id="empezar_tipo">
            <option value="" <?php echo $empezar_tipo == "" ? " selected ":"" ?>>Seleccione</option>
            <option value="pedido" <?php echo $empezar_tipo == "pedido" ? " selected ":"" ?>>Pedido</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <select style="" class="form-control" name="empezar_uuid" id="empezar_uuid" disabled="">
            <option value="">Seleccione</option>
            <?php foreach($pedidos as $pedido):?>
            <option value="<?php echo $pedido->uuid_pedido?>" <?php echo $empezar_uuid == $pedido->uuid_pedido ? " selected ":"" ?>><?php echo "PD".$pedido->numero?></option>
            <?php endforeach;?>
        </select>
    </div>
</div>