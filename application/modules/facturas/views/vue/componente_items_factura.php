<template id="items_factura">

	<div class="ibox-content">

            <!-- Componentes Dinamicos -->
            <div v-ref:items :is="currentView" :categorias="categorias" :impuestos="impuestos" :cuenta_transaccionales="cuenta_transaccionales" :factura.sync="factura"></div>
            <!-- /Componentes Dinamicos -->

            <!-- Sumatoria Totales -->
            <input type="hidden" v-model="delete_items" name="delete_items">
            <table class="table table-noline tabla-dinamica" id="cotizacionDinamica">
                <tfoot>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"> <span>Subtotal: </span><span id="tsubtotal" class="sum-total">${{subtotal}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[subtotal]" v-model="subtotal" /></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span>Descuento:</span> <span id="timpuesto" class="sum-total">${{descuento}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[descuento]" id="hdescuento" v-model="descuento" /></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span>Impuesto:</span> <span id="timpuesto" class="sum-total">${{impuesto}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[impuestos]" id="himpuesto" v-model="impuesto" /></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span>Total: </span> <span id="ttotal" class="sum-total">${{total}}</span></td>
                        <td width="5%"><input type="hidden" name="campo[total]" v-model="total"></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span class="label label-successful">Cobros </span> <span class="sum-total">${{cobros}}</span></td>
                        <td width="5%"></td>
                    </tr>
                    <tr>
                        <td width="75%"></td>
                        <td width="20%" class="sum-border"><span class="label label-danger">Saldo </span> <span class="sum-total">${{saldo}}</span></td>
                        <td width="5%"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

</template>
