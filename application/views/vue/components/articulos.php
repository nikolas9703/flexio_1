

<template id="articulos_template">

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-noline tabla-dinamica itemsTable">
                    <thead>
                        <tr>
                            <th width="1%" style="background: white;" v-if="precioCompra() || precioVenta()"></th>
                            <th width="7.9166666666667%" class="categoria ">Categoría <span class="required" aria-required="true">*</span></th>
                            <th width="7.9166666666667%" class="item ">Item <span class="required" aria-required="true">*</span></th>
                            <th width="7.9166666666667%" class="atributo ">Atributo </th>
                            <th width="7.9166666666667%" class="cantidad ">Cantidad <span class="required" aria-required="true">*</span></th>
                            <th width="7.9166666666667%" class="unidad ">Unidad <span class="required" aria-required="true">*</span></th>
                            <th width="7.9166666666667%" class="precio_unidad " v-if="precioCompra() || precioVenta()">Precio unidad <span class="required" aria-required="true">*</span></th>
                            <th width="7.9166666666667%" class="precio_total " v-if="precioCompra() || precioVenta()">Subtotal </th>
                            <th width="7.9166666666667%" class="cuenta " v-if="!( precioCompra() || precioVenta() )">Cuenta </th>
                            <th width="1%" style="background: white;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!--componente articulo-->
                        <tr v-for="row in detalle.articulos" :is="articulo" :config="config" :detalle.sync="detalle" :catalogos="catalogos" :parent_index="$index" :row.sync="row" :empezable.sync="empezable"></tr>

                        <tr v-if="precioCompra() || precioVenta()">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Subtotal:</td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{showRetenido?getSubTotal - getRetenidoTotal:getSubTotal | currency}}</td>
                            <td><br></td>
                        </tr>
                        <tr v-if="precioCompra() || precioVenta()">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Descuento:</td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getDescuentoTotal | currency}}</td>
                            <td><br></td>
                        </tr>
                        <tr v-if="precioCompra() || precioVenta()">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Impuesto:</td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getImpuestoTotal | currency}}</td>
                            <td><br></td>
                        </tr>
                        <tr v-if="precioCompra() || precioVenta()">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">Total:</td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{ showRetenido? getTotal - getRetenidoTotal:getTotal | currency}}</td>
                            <td>
                                <br>
                                <input type="hidden" name="campo[subtotal]" :value="showRetenido?getSubTotal - getRetenidoTotal:getSubTotal">
                                <input type="hidden" name="campo[descuento]" :value="getDescuentoTotal">
                                <input type="hidden" name="campo[impuesto]" :value="getImpuestoTotal">
                                <input type="hidden" name="campo[impuestos]" :value="getImpuestoTotal">
                                <input type="hidden" name="campo[total]" :value="showRetenido? getTotal - getRetenidoTotal:getTotal">
                                <input type="hidden" name="campo[monto]" :value="showRetenido? getTotal - getRetenidoTotal:getTotal">
                            </td>
                        </tr>
                        <tr v-if="showRetenido">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                                <span class="label label-warning">Retenido </span>
                            </td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{getRetenidoTotal | currency}}</td>
                            <td><br></td>
                        </tr>
                        <tr v-if="showPagos()">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                                <span class="label label-successful">Pagos </span>
                            </td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{detalle.pagos | currency}}</td>
                            <td><br></td>
                        </tr>
                        <tr v-if="showSaldo()">
                            <td colspan="6"><br></td>
                            <td style="border: 1px solid silver !important;border-right: 0px !important;font-weight: bold">
                                <span class="label label-danger">Saldo </span>
                            </td>
                            <td style="border: 1px solid silver !important;border-left: 0px !important;font-weight: bold;text-align: right">{{detalle.saldo | currency}}</td>
                            <td><br></td>
                        </tr>
                    </tbody>
                </table>
                <span class="tabla_dinamica_error"></span>

            </div>
        </div>
    </div>


</template>

<style type="text/css">
    .table-noline .table-noline td .totales_derecha{
        padding: 10px;
        border: 1px solid silver !important;
        border-left: 0px !important;
    }

    .table-noline .table-noline td .totales_izquierda{
        padding: 10px;
        border: 1px solid silver !important;
        border-right: 0px !important;
    }
</style>
