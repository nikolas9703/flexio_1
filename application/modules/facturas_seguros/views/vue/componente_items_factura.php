<template id="items_factura">

    <div class="ibox-content row">
              
            <!-- Componentes Dinamicos -->
            <div v-ref:items :is="currentView" :categorias="categorias" :impuestos="impuestos" :cuenta_transaccionales="cuenta_transaccionales" :factura.sync="factura"></div>
            <!-- /Componentes Dinamicos -->

            <!-- Sumatoria Totales -->
            <input type="hidden" v-model="delete_items" name="delete_items">

            <table class="table table-noline tabla-dinamica" id="cotizacionDinamica">
                <tfoot>
                    <tr>
                        <td width="75%"></td>
                        <!--<td width="20%" class="sum-border"> <span>Subtotal: </span><span id="tsubtotal" class="sum-total">${{subtotal}}</span></td>-->
                        <td width="20%" class="sum-border"> <span>Subtotal: </span><span id="tsubtotal" class="sum-total">${{factura.subtotal}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[subtotal]" v-model="factura.subtotal" /></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span>Descuento:</span> <span id="tdescuento" class="sum-total">${{factura.descuento}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[descuento]" id="hdescuento" v-model="factura.descuento" /></td>
                    </tr>
                    <tr id="muestraotros" style="display:none">
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span>Otros:</span> <span id="totros" class="sum-total">${{factura.otros}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[otros]" id="hotros" v-model="factura.otros" /></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span>Impuesto:</span> <span id="timpuesto" class="sum-total">${{factura.impuesto}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[impuestos]" id="himpuesto" v-model="factura.impuesto" /></td>
                    </tr>                    
                    <tr>
                        <td width="75%"><label>Observaciones </label></td>
                        <td width="20%" class="sum-border"><span>Total: </span> <span id="ttotal" class="sum-total">${{factura.total}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[total]" v-model="factura.total"></td>
                    </tr>
                    <tr>
                        <td width="75%" rowspan="2"><textarea id="comentario" name="campo[comentario]" v-model="factura.comentario" class="form-control" style="width:75%"></textarea></td>
                        <td width="20%" class="sum-border"><span class="label label-successful">Cobros </span> <span class="sum-total">${{cobros}}</span></td>
                        <td width="5%"></td>
                    </tr>
                    <tr>
                        <td width="20%" class="sum-border" style="padding-left: 8px !important"><span class="label label-danger">Saldo </span> <span class="sum-total">${{saldo}}</span></td>
                        <td width="5%"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

</template>



