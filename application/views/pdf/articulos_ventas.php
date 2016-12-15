
    

<!--tabla de items-->
        <tr>
            <td colspan="2">
                
                <table style="width: 100%;" class="tabla_items">
                    <thead>
                        <tr>
                            <th>Categor&iacute;a</th>
                            <th>Item</th>
                            <th>Atributos</th>
                            <th>Cant.</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($venta->items as $item):?>
                        <tr>
                            <td><?php echo $item->categoria->nombre;?></td>
                            <td><?php echo $item->inventario_item->nombre;?></td>
                            <td><?php echo count($item->atributo) ? $item->atributo->nombre : $item->atributo_text;?></td>
                            <td class="numero"><?php echo $item->cantidad;?></td>
                            <td style="text-align: center;"><?php echo $item->unidad->nombre;?></td>
                            <td class="numero"><?php echo $item->precio_unidad;?></td>
                            <td class="numero"><?php echo $item->descuento;?></td>
                            <td class="numero"><?php echo $item->precio_total;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                
            </td>
        </tr>
        
        <!--pie de tabla de items-->
        <tr>
            <td>T&eacute;rminos de pago: <?php echo $venta->termino_pago_catalogo->valor;?></td>
            <td rowspan="3">
            
                <table style="width: 100%">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="numero">$<?php echo $venta->subtotal?></td>
                    </tr>
                    <tr>
                        <td>Descuento:</td>
                        <td class="numero">$<?php echo $venta->descuento?></td>
                    </tr>
                    <tr>
                        <td>Impuesto:</td>
                        <td class="numero">$<?php echo $venta->impuestos?></td>
                    </tr>
                    <tr>
                        <td>Valor total de la orden:</td>
                        <td class="numero">$<?php echo $venta->total?></td>
                    </tr>
                </table>
                
            </td>
        </tr>


